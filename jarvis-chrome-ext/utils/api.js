// utils/api.js

let API_BASE_URL = "https://jarvisbot.biz/api"; // URL por defecto

/**
 * Configura la URL base de la API.
 * @param {string} url - La nueva URL base.
 */
export function setApiBaseUrl(url) {
    API_BASE_URL = url;
    console.log(`API base URL configurada a: ${API_BASE_URL}`);
}

/**
 * Realiza una solicitud a la API de JarvisBot.
 * @param {string} endpoint - El endpoint de la API.
 * @param {Object} options - Opciones de la solicitud.
 * @returns {Promise} - Promesa que resuelve con los datos de la respuesta.
 */
export async function fetchFromApi(endpoint, options = {}) {
    try {
        const url = `${API_BASE_URL}${endpoint}`;
        const token = await getJwtToken();

        const response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                'Content-Type': 'application/json',
                'Authorization': token ? `Bearer ${token}` : undefined,
            },
        });

        if (!response.ok) {
            if (response.status === 401) {
                await clearUserSession();
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log(`Datos recibidos exitosamente de ${endpoint}:`, data);
        return data;
    } catch (error) {
        console.error(`Error en fetchFromApi para ${endpoint}:`, error);
        throw error;
    }
}

/**
 * Obtiene el token JWT del almacenamiento local.
 * @returns {Promise<string|null>} - Promesa que resuelve con el token JWT o null.
 */
export function getJwtToken() {
    return new Promise((resolve) => {
        chrome.storage.local.get("jwtToken", (data) => {
            resolve(data.jwtToken || null);
        });
    });
}

/**
 * Realiza una solicitud autenticada a la API de JarvisBot.
 * @param {string} endpoint - El endpoint de la API.
 * @param {Object} options - Opciones de la solicitud.
 * @returns {Promise} - Promesa que resuelve con los datos de la respuesta.
 */
export async function authenticatedFetch(endpoint, options = {}) {
    const token = await getJwtToken();
    if (!token) {
        console.error("No se encontró token JWT");
        throw new Error("No autorizado");
    }

    const authOptions = {
        ...options,
        headers: {
            ...options.headers,
            'Authorization': `Bearer ${token}`,
        },
    };

    return fetchFromApi(endpoint, authOptions);
}

/**
 * Limpia la sesión del usuario.
 */
export async function clearUserSession() {
    console.log("Limpiando sesión del usuario");
    await chrome.storage.local.remove(["userInfo", "jwtToken"]);
    chrome.runtime.sendMessage({ action: "sessionCleared" });
    console.log("Sesión del usuario limpiada");
}
