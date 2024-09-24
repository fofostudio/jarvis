// background.js

import { setApiBaseUrl, fetchFromApi } from "./utils/api.js";

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
            ); // Muestra solo una parte del token por seguridad

            console.log("Intentando obtener datos del usuario con el token");

            const userData = await fetchUserData(jwtToken);

            if (userData) {
                console.log(
                    "Datos del usuario obtenidos exitosamente:",
                    JSON.stringify(userData, null, 2)
                );

                // Actualizar la información del usuario en el almacenamiento local
                await chrome.storage.local.set({ userInfo: userData });
                console.log(
                    "Información del usuario actualizada en el almacenamiento local"
                );

                // Actualizar el estado de la aplicación
                updateAppState(userData);
                console.log("Estado de la aplicación actualizado");

                // Notificar a los componentes de la extensión sobre la actualización
                chrome.runtime.sendMessage({
                    action: "sessionUpdated",
                    userData: userData,
                });
                console.log("Notificación de sesión actualizada enviada");
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
        console.error("Stack trace:", error.stack);
        await clearUserSession();
    }
    console.log("Finalizando checkAndUpdateSession");
}

// Función auxiliar para actualizar el estado de la aplicación
function updateAppState(userData) {
    // Aquí puedes agregar lógica para actualizar el estado global de tu aplicación
    // Por ejemplo, actualizar el estado de Redux, o variables globales
    console.log(
        "Actualizando estado de la aplicación con los datos del usuario"
    );
    // Ejemplo: window.appState.currentUser = userData;
}

// Función auxiliar para limpiar la sesión del usuario
async function clearUserSession() {
    console.log("Limpiando sesión del usuario");
    await chrome.storage.local.remove(["userInfo", "jwtToken"]);
    // Limpiar cualquier otro dato de sesión que puedas tener

    // Notificar a los componentes de la extensión sobre la limpieza de la sesión
    chrome.runtime.sendMessage({ action: "sessionCleared" });
    console.log("Notificación de sesión limpiada enviada");
}

// Asegúrate de que estas funciones estén definidas o importadas correctamente
// async function getJwtToken() { ... }
// async function fetchUserData(token) { ... }

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
        const response = await fetchFromApi("/user-info");

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

// Añadir tarea a la cola
function addTaskToQueue(task) {
    taskQueue.push(task);
    if (!isProcessingQueue) {
        processNextTask();
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

// Exportar funciones que puedan ser necesarias en otros scripts
export { checkAndUpdateSession, sendNotification };
