// background.js

let API_BASE_URL = "https://jarvisbot.biz/api";

chrome.runtime.onInstalled.addListener(() => {
    console.log("Extensión JarvisBot instalada");
    checkAndInitializeSession();
});

async function checkAndInitializeSession() {
    try {
        const userData = await getUserData();
        if (userData) {
            console.log("Sesión inicializada con éxito");
            chrome.storage.local.set({ userInfo: userData });
        } else {
            console.log("No se pudo obtener los datos del usuario");
        }
    } catch (error) {
        console.error("Error al inicializar la sesión:", error);
    }
}

async function getUserData() {
    try {
        const response = await fetchFromApi("/user");
        if (response.success && response.user) {
            return response.user;
        } else {
            throw new Error("Respuesta del servidor no válida");
        }
    } catch (error) {
        console.error("Error en getUserData:", error);
        throw error;
    }
}

async function fetchFromApi(endpoint, options = {}) {
    try {
        const url = `${API_BASE_URL}${endpoint}`;
        const response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            credentials: 'include'  // Esto es crucial para incluir las cookies en la solicitud
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`Error en fetchFromApi para ${endpoint}:`, error);
        throw error;
    }
}