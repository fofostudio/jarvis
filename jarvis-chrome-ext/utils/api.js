let API_BASE_URL = "https://jarvisbot.biz/api";

export function setApiBaseUrl(url) {
    API_BASE_URL = url;
    console.log(`API base URL configurada a: ${API_BASE_URL}`);
}

export async function fetchFromApi(endpoint, options = {}) {
    try {
        const url = `${API_BASE_URL}${endpoint}`;
        const response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                "Content-Type": "application/json",
            },
            credentials: 'include',  // Esto es crucial para incluir las cookies de sesión
        });

        if (!response.ok) {
            if (response.status === 401) {
                await clearUserSession();
                throw new Error("Sesión expirada");
            }
            const errorBody = await response.text();
            throw new Error(
                `HTTP error! status: ${response.status}, body: ${errorBody}`
            );
        }

        const data = await response.json();
        console.log(`Datos recibidos exitosamente de ${endpoint}:`, data);
        return data;
    } catch (error) {
        console.error(`Error en fetchFromApi para ${endpoint}:`, error);
        throw error;
    }
}

export async function authenticatedFetch(endpoint, options = {}) {
    return fetchFromApi(endpoint, options);
}

export async function clearUserSession() {
    console.log("Limpiando sesión del usuario");
    await chrome.storage.local.remove(["userInfo"]);
    try {
        await chrome.runtime.sendMessage({ action: "sessionCleared" });
    } catch (error) {
        console.log("No hay receptor para el mensaje de sesión limpiada");
    }
    console.log("Sesión del usuario limpiada");
}

export async function getUserData() {
    try {
        const response = await fetchFromApi('/api/user');
        if (response.success && response.user) {
            await chrome.storage.local.set({ 
                userData: response.user
            });
            return response.user;
        } else {
            throw new Error('Datos de usuario incompletos');
        }
    } catch (error) {
        console.error('Error al obtener datos del usuario:', error);
        throw error;
    }
}
export async function checkSession() {
    try {
        const userData = await getUserData();
        return !!userData;
    } catch (error) {
        console.error('Error al verificar la sesión:', error);
        return false;
    }
}

export async function attemptAutoLogin() {
    try {
        const isSessionValid = await checkSession();
        return isSessionValid;
    } catch (error) {
        console.error('Error en el intento de auto-login:', error);
        return false;
    }
}