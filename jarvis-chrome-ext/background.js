// background.js

import { setApiBaseUrl, fetchFromApi, authenticatedFetch, clearUserSession } from './utils/api.js';
setApiBaseUrl("https://jarvisbot.biz/api");

// Configuración
const JWT_COOKIE_NAME = "jwt_token";
const CHECK_SESSION_INTERVAL = 5 * 60 * 1000; // 5 minutos

let userInfo = null;
let taskQueue = [];
let isProcessingQueue = false;
let connections = {};

// Inicialización
chrome.runtime.onInstalled.addListener(() => {
    console.log("Extensión JarvisBot instalada");
    checkAndUpdateSession();
    chrome.alarms.create("checkSession", { periodInMinutes: 5 });
});

chrome.cookies.onChanged.addListener(async (changeInfo) => {
    if (
        changeInfo.cookie.name === JWT_COOKIE_NAME &&
        changeInfo.cause === "explicit"
    ) {
        if (changeInfo.removed) {
            await clearUserSession();
        } else {
            const jwtToken = changeInfo.cookie.value;
            await chrome.storage.local.set({ jwtToken });
            const userData = await fetchUserData(jwtToken);
            if (userData) {
                await storeUserInfo(userData);
                userInfo = userData;
                broadcastMessage({ action: "sessionUpdated", userData });
            } else {
                await clearUserSession();
            }
        }
    }
});
// Verificar sesión periódicamente
chrome.alarms.onAlarm.addListener((alarm) => {
    if (alarm.name === "checkSession") {
        checkAndUpdateSession();
    }
});

// Manejar conexiones de los content scripts
chrome.runtime.onConnect.addListener((port) => {
    const tabId = port.sender.tab.id;
    connections[tabId] = port;

    port.onDisconnect.addListener(() => {
        delete connections[tabId];
    });

    port.onMessage.addListener((msg) => {
        console.log("Mensaje recibido del content script:", msg);
        // Manejar mensajes aquí
    });
});

async function checkAndUpdateSession() {
    console.log("Iniciando checkAndUpdateSession");
    try {
        const jwtToken = await getJwtToken();
        if (jwtToken) {
            console.log(
                "Token JWT obtenido:",
                jwtToken.substring(0, 20) + "..."
            );

            const userData = await fetchUserData(jwtToken);

            if (userData) {
                console.log(
                    "Datos del usuario obtenidos exitosamente:",
                    userData
                );

                await storeUserInfo(userData);
                console.log("Información del usuario almacenada");

                userInfo = userData;
                broadcastMessage({ action: "sessionUpdated", userData });
                console.log(
                    "Mensaje de sesión actualizada enviado a los content scripts"
                );
            } else {
                console.log("No se pudo obtener datos del usuario");
                await clearUserSession();
            }
        } else {
            console.log("No se encontró token JWT");
            await clearUserSession();
        }
    } catch (error) {
        console.error("Error al verificar sesión:", error);
        await clearUserSession();
    }
    console.log("Finalizando checkAndUpdateSession");
}

// Obtener token JWT de las cookies
function getJwtToken() {
    return new Promise((resolve) => {
        chrome.cookies.get(
            { url: "https://jarvisbot.biz", name: JWT_COOKIE_NAME },
            (cookie) => {
                if (cookie) {
                    console.log("Token JWT encontrado:", cookie.value);
                    resolve(cookie.value);
                } else {
                    console.log("No se encontró el token JWT");
                    resolve(null);
                }
            }
        );
    });
}

async function fetchUserData(token) {
    try {
        const response = await fetchFromApi("/user-info", {
            headers: { Authorization: `Bearer ${token}` },
        });

        console.log("Respuesta completa de /user-info:", response);

        if (!response.success) {
            console.error(
                "Error al obtener datos del usuario:",
                response.error
            );
            return null;
        }

        return response.userInfo;
    } catch (error) {
        console.error("Error inesperado al obtener datos del usuario:", error);
        return null;
    }
}

// Almacenar información del usuario
async function storeUserInfo(userData) {
    return new Promise((resolve) => {
        chrome.storage.local.set({ userInfo: userData }, resolve);
    });
}

// Manejar mensajes
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    switch (request.action) {
        case "getUserInfo":
            sendResponse({ userInfo: userInfo });
            break;
        case "executeTask":
            addTaskToQueue(request.task);
            sendResponse({ message: "Tarea añadida a la cola" });
            break;
        case "checkSession":
            checkAndUpdateSession();
            sendResponse({ message: "Verificando sesión" });
            break;
    }
    return true;
});
function checkSessionCookie() {
    chrome.cookies.get(
        { url: "https://jarvisbot.biz", name: "jwt_token" },
        function (cookie) {
            if (cookie) {
                // La cookie existe, verificar el token
                verifyToken(cookie.value);
            } else {
                // No hay cookie, el usuario no está logueado
                clearUserSession();
            }
        }
    );
}

function verifyToken(token) {
    fetch("https://jarvisbot.biz/api/auth/validate-token", {
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.user) {
                // Token válido, almacenar información del usuario
                chrome.storage.local.set({ userInfo: data.user });
            } else {
                // Token inválido
                clearUserSession();
            }
        })
        .catch((error) => {
            console.error("Error validando token:", error);
            clearUserSession();
        });
}
function loadOperatorInfo() {
    chrome.storage.local.get("userInfo", function (data) {
        if (data.userInfo) {
            fetch("https://jarvisbot.biz/api/operator-info", {
                method: "GET",
                headers: {
                    Authorization: `Bearer ${data.userInfo.token}`,
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    chrome.storage.local.set({ operatorInfo: data });
                })
                .catch((error) =>
                    console.error(
                        "Error cargando información del operador:",
                        error
                    )
                );
        }
    });
}
// Añadir tarea a la cola
function addTaskToQueue(task) {
    if (
        userInfo &&
        userInfo.platforms.includes(task.platform) &&
        userInfo.groups.some((group) => group.name === task.group)
    ) {
        taskQueue.push(task);
        if (!isProcessingQueue) {
            processNextTask();
        }
    } else {
        console.log(
            `Tarea no aplicable para la plataforma ${task.platform} y grupo ${task.group} del usuario`
        );
    }
}
// Procesar siguiente tarea en la cola
async function processNextTask() {
    if (taskQueue.length === 0) {
        isProcessingQueue = false;
        return;
    }

    isProcessingQueue = true;
    const task = taskQueue.shift();

    try {
        const tab = await findOrCreateTab(task.platform);
        await executeTaskInTab(tab.id, task);
    } catch (error) {
        console.error("Error al ejecutar tarea:", error);
        sendNotification(
            "Error en la tarea",
            `No se pudo ejecutar la tarea en ${task.platform}`
        );
    }

    processNextTask();
}

// Encontrar o crear pestaña para la plataforma
async function findOrCreateTab(platform) {
    return new Promise((resolve) => {
        chrome.tabs.query({ url: `*://${platform}/*` }, (tabs) => {
            if (tabs.length > 0) {
                chrome.tabs.update(tabs[0].id, { active: true });
                resolve(tabs[0]);
            } else {
                chrome.tabs.create({ url: `https://${platform}` }, resolve);
            }
        });
    });
}

// Ejecutar tarea en pestaña
async function executeTaskInTab(tabId, task) {
    return new Promise((resolve, reject) => {
        if (connections[tabId]) {
            connections[tabId].postMessage({
                action: "executeTask",
                task: task,
            });
            resolve();
        } else {
            reject(new Error("No hay conexión con la pestaña"));
        }
    });
}

// Manejar cambios en las pestañas
chrome.tabs.onUpdated.addListener((tabId, changeInfo, tab) => {
    if (changeInfo.status === "complete" && tab.url) {
        const url = new URL(tab.url);
        if (userInfo && userInfo.platforms.includes(url.hostname)) {
            sendMessageToTab(tabId, {
                action: "initPlatform",
                platform: url.hostname,
                userInfo: userInfo,
            });
        }
    }
});

// Función para enviar mensajes a una pestaña específica
function sendMessageToTab(tabId, message) {
    if (connections[tabId]) {
        connections[tabId].postMessage(message);
    } else {
        console.log(`No hay conexión con la pestaña ${tabId}`);
    }
}

// Función para enviar mensajes a todas las pestañas conectadas
function broadcastMessage(message) {
    Object.values(connections).forEach((port) => port.postMessage(message));
}

// Función para enviar notificaciones
function sendNotification(title, message) {
    chrome.notifications.create({
        type: "basic",
        iconUrl: "icons/icon128.png",
        title: title,
        message: message,
    });
}

console.log("Background script loaded");

chrome.runtime.onInstalled.addListener(() => {
    console.log("Extension installed");
    // Más código de inicialización
});
