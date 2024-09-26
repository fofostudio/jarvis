// background.js

import {
    getJwtToken,
    fetchFromApi,
    clearUserSession,
    checkAndRefreshToken,
    attemptAutoLogin,
} from "./utils/api.js";

async function initializeSession(retries = 3) {
    try {
        const autoLoginSuccessful = await attemptAutoLogin();
        if (autoLoginSuccessful) {
            console.log('Auto-login exitoso');
        }

        const token = await checkAndRefreshToken();
        if (!token) {
            console.log("No se pudo obtener un token JWT válido");
            return null;
        }

        const response = await fetchFromApi("/user-info");
        if (response.success && response.user) {
            await chrome.storage.local.set({ userInfo: response.user });
            console.log("Sesión inicializada con éxito");
            return response.user;
        } else {
            console.log("Respuesta del servidor no válida");
            return null;
        }
    } catch (error) {
        console.error("Error al inicializar la sesión:", error);
        if (retries > 0) {
            console.log(`Reintentando inicialización de sesión... (${retries} intentos restantes)`);
            await new Promise(resolve => setTimeout(resolve, 5000)); // Espera 5 segundos
            return initializeSession(retries - 1);
        } else {
            await clearUserSession();
            return null;
        }
    }
}

chrome.runtime.onInstalled.addListener(() => {
    console.log("Extensión JarvisBot instalada");
    initializeSession();
    chrome.alarms.create("checkSession", { periodInMinutes: 5 });
});

chrome.alarms.onAlarm.addListener((alarm) => {
    if (alarm.name === "checkSession") {
        initializeSession();
    }
});

chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request.action === "executeTask") {
        executeTask(request.platform, request.task, request.girlData);
    } else if (request.action === "getUserInfo") {
        getUserInfo().then(sendResponse);
        return true; // Indicates that the response is asynchronous
    }
});

async function executeTask(platform, task, girlData) {
    try {
        await checkAndRefreshToken();
        const [tab] = await chrome.tabs.query({
            active: true,
            currentWindow: true,
        });
        const response = await chrome.tabs.sendMessage(tab.id, {
            action: "executeTask",
            platform,
            task,
            girlData,
        });
        console.log(`Tarea ${task} ejecutada en ${platform}:`, response);
        chrome.runtime.sendMessage({
            action: "taskCompleted",
            platform,
            task,
            result: response,
        });
    } catch (error) {
        console.error(
            `Error al ejecutar la tarea ${task} en ${platform}:`,
            error
        );
        chrome.runtime.sendMessage({
            action: "taskFailed",
            platform,
            task,
            error: error.message,
        });
    }
}

async function getUserInfo() {
    try {
        const token = await checkAndRefreshToken();
        if (!token) {
            throw new Error("No se pudo obtener un token válido");
        }

        const response = await fetchFromApi("/user-info");
        if (response.success && response.user) {
            await chrome.storage.local.set({ userInfo: response.user });
            return response.user;
        } else {
            throw new Error("Respuesta del servidor no válida");
        }
    } catch (error) {
        console.error("Error al obtener la información del usuario:", error);
        await clearUserSession();
        return null;
    }
}

chrome.tabs.onUpdated.addListener((tabId, changeInfo, tab) => {
    if (changeInfo.status === "complete") {
        const platform = detectPlatform(tab.url);
        if (platform) {
            chrome.tabs.sendMessage(tabId, {
                action: "platformDetected",
                platform,
            });
        }
    }
});

function detectPlatform(url) {
    const platforms = ["udate", "talkytimes", "amolatina"];
    return platforms.find((platform) => url.includes(platform)) || null;
}

// Listener para mensajes de content scripts
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.action === "logMessage") {
        console.log("Log from content script:", message.data);
    }
});

// Función para enviar un mensaje a un content script específico
async function sendMessageToContentScript(tabId, message) {
    try {
        const response = await chrome.tabs.sendMessage(tabId, message);
        console.log("Response from content script:", response);
        return response;
    } catch (error) {
        console.error("Error sending message to content script:", error);
    }
}

// Función para obtener la pestaña activa
async function getActiveTab() {
    const [tab] = await chrome.tabs.query({
        active: true,
        currentWindow: true,
    });
    return tab;
}

// Función para crear una nueva pestaña
async function createNewTab(url) {
    return await chrome.tabs.create({ url: url });
}

// Función para actualizar una pestaña existente
async function updateTab(tabId, updateProperties) {
    return await chrome.tabs.update(tabId, updateProperties);
}