import API from "/api.js";

let refreshTokenInterval;
let activeTasks = new Map();
let contentScriptReady = false;

chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    console.log("Mensaje recibido en background:", request);
    switch (request.action) {
        case "contentScriptReady":
            contentScriptReady = true;
            console.log('Content script is ready');
            sendResponse({ success: true });
            break;
        case "startTokenRefresh":
            startTokenRefreshInterval();
            sendResponse({ success: true });
            break;
        case "stopTokenRefresh":
            stopTokenRefreshInterval();
            sendResponse({ success: true });
            break;
        case "startTask":
            startTask(request.taskName, request.platform, request.tabId)
                .then(sendResponse)
                .catch((error) =>
                    sendResponse({ success: false, error: error.message })
                );
            return true; // Indica que la respuesta será asíncrona
        case "taskProgress":
            updateTaskProgress(sender.tab.id, request.progress);
            break;
        case "taskComplete":
            completeTask(sender.tab.id, request.result);
            break;
        default:
            console.warn("Acción no reconocida:", request.action);
            sendResponse({ success: false, error: "Acción no reconocida" });
    }
});

function startTokenRefreshInterval() {
    if (refreshTokenInterval) {
        clearInterval(refreshTokenInterval);
    }
    refreshTokenInterval = setInterval(async () => {
        try {
            const result = await API.refreshToken();
            if (!result.success) {
                throw new Error("Failed to refresh token");
            }
            console.log("Token refreshed successfully");
        } catch (error) {
            console.error("Token refresh failed:", error);
            stopTokenRefreshInterval();
            chrome.runtime.sendMessage({ action: "sessionExpired" });
        }
    }, 55 * 60 * 1000); // 55 minutos
}

function stopTokenRefreshInterval() {
    if (refreshTokenInterval) {
        clearInterval(refreshTokenInterval);
        refreshTokenInterval = null;
    }
}

async function startTask(taskName, platform, tabId) {
    console.log(`Iniciando tarea: ${taskName} para plataforma: ${platform} en pestaña: ${tabId}`);
    if (activeTasks.has(tabId)) {
        return { success: false, error: 'Ya hay una tarea en ejecución en esta pestaña' };
    }

    activeTasks.set(tabId, { taskName, platform, status: 'running', progress: 0 });

    try {
        await injectContentScript(tabId);
        await waitForContentScript(tabId);
        const response = await chrome.tabs.sendMessage(tabId, {
            action: "executeTask",
            taskName: taskName,
            platform: platform,
        });
        console.log("Respuesta del content script:", response);
        return {
            success: true,
            message: `Tarea ${taskName} iniciada en la pestaña ${tabId}`,
        };
    } catch (error) {
        activeTasks.delete(tabId);
        console.error("Error al iniciar la tarea:", error);
        return { success: false, error: error.message };
    }
}
async function waitForContentScript(tabId) {
    return new Promise((resolve, reject) => {
        const timeout = setTimeout(() => {
            reject(new Error('Timeout waiting for content script'));
        }, 10000); // 10 segundos de timeout

        function checkContentScript() {
            chrome.tabs.sendMessage(tabId, {action: 'ping'}, response => {
                if (chrome.runtime.lastError) {
                    // Si hay un error, esperamos un poco y volvemos a intentar
                    setTimeout(checkContentScript, 100);
                } else if (response && response.pong) {
                    clearTimeout(timeout);
                    resolve();
                }
            });
        }

        checkContentScript();
    });
}
function updateTaskProgress(tabId, progress) {
    console.log(
        `Actualizando progreso de tarea en pestaña ${tabId}: ${progress}%`
    );
    if (activeTasks.has(tabId)) {
        const task = activeTasks.get(tabId);
        task.progress = progress;
        chrome.runtime.sendMessage({
            action: "taskProgress",
            tabId,
            taskName: task.taskName,
            progress,
        });
    }
}

function completeTask(tabId, result) {
    console.log(`Completando tarea en pestaña ${tabId}:`, result);
    if (activeTasks.has(tabId)) {
        const task = activeTasks.get(tabId);
        activeTasks.delete(tabId);
        chrome.runtime.sendMessage({
            action: "taskComplete",
            tabId,
            taskName: task.taskName,
            result,
        });
    }
}

chrome.runtime.onInstalled.addListener((details) => {
    if (details.reason === "install" || details.reason === "update") {
        console.log(
            "Extensión instalada o actualizada. Iniciando intervalo de actualización de token."
        );
        startTokenRefreshInterval();
    }
});

chrome.runtime.onStartup.addListener(() => {
    console.log(
        "Extensión iniciada. Iniciando intervalo de actualización de token."
    );
    startTokenRefreshInterval();
});

chrome.tabs.onRemoved.addListener((tabId) => {
    if (activeTasks.has(tabId)) {
        console.log(`Pestaña ${tabId} cerrada. Limpiando tarea activa.`);
        activeTasks.delete(tabId);
    }
});

async function checkSessionStatus() {
    try {
        const result = await API.getUserData();
        if (!result.success) {
            throw new Error("Failed to get user data");
        }
        return true;
    } catch (error) {
        console.error("Session check failed:", error);
        return false;
    }
}

setInterval(async () => {
    const isSessionValid = await checkSessionStatus();
    if (!isSessionValid) {
        stopTokenRefreshInterval();
        chrome.runtime.sendMessage({ action: "sessionExpired" });
    }
}, 60 * 60 * 1000); // Cada hora

async function injectAndExecuteTask(tabId, taskName) {
    await chrome.scripting.executeScript({
        target: {tabId: tabId},
        files: ['scripts/udate.js']
    });

    return chrome.tabs.sendMessage(tabId, {
        action: 'executeTask',
        taskName: taskName
    });
}
async function injectContentScript(tabId) {
    return chrome.scripting.executeScript({
        target: {tabId: tabId},
        files: ['scripts/udate.js']
    });
}

console.log("Background script loaded and running.");
