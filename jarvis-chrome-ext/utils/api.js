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
 * @param {Object} options - Opciones de la solicitud (método, headers, body).
 * @returns {Promise} - Promesa que resuelve con los datos de la respuesta.
 */
export async function fetchFromApi(endpoint, options = {}) {
    try {
        // Obtener el token JWT
        let token = await getJwtToken();

        if (!token) {
            console.error("No se pudo obtener el token JWT para la solicitud API");
            throw new Error("Token JWT no disponible");
        }

        console.log(`Token JWT obtenido para la solicitud: ${token.substring(0, 20)}...`);

        const url = `${API_BASE_URL}${endpoint}`;
        const defaultOptions = {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
            credentials: "include",
        };

        const requestOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers,
            },
        };

        console.log(`Realizando solicitud a: ${url}`);
        console.log("Opciones de la solicitud:", JSON.stringify(requestOptions, null, 2));

        // Realizar la solicitud inicial
        let response = await fetch(url, requestOptions);

        console.log(`Respuesta recibida. Status: ${response.status}`);
        console.log("Headers:", Object.fromEntries(response.headers.entries()));

        // Verificar si el token es inválido y si es 401 (no autorizado)
        if (response.status === 401) {
            console.warn("Token expirado o inválido, intentando renovar...");

            // Intenta refrescar el token
            token = await refreshJwtToken();
            if (!token) {
                throw new Error("No se pudo renovar el token.");
            }

            console.log(`Nuevo token obtenido: ${token.substring(0, 20)}...`);

            // Reintentar la solicitud con el nuevo token
            requestOptions.headers.Authorization = `Bearer ${token}`;
            console.log("Realizando solicitud nuevamente con nuevo token...");

            response = await fetch(url, requestOptions);

            console.log(`Respuesta de reintento recibida. Status: ${response.status}`);
        }

        const responseText = await response.text();

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error("La respuesta no es JSON válido. Recibido:", responseText.substring(0, 500));
            throw new Error(`Respuesta no válida del servidor. Status: ${response.status}, URL: ${response.url}`);
        }

        if (!response.ok) {
            console.error(`Error en la respuesta. Status: ${response.status}, Mensaje:`, data.error || 'Error desconocido');
            throw new Error(data.error || `Error del servidor. Status: ${response.status}`);
        }

        if (!data.success) {
            console.error("La respuesta indica fallo:", data.error || 'Error desconocido');
            throw new Error(data.error || `Error desconocido. Status: ${response.status}`);
        }

        console.log("Datos recibidos exitosamente:", JSON.stringify(data, null, 2));
        return data;
    } catch (error) {
        console.error("Error en fetchFromApi:", error);
        throw error;
    }
}

// Asegúrate de que estas funciones estén definidas o importadas correctamente
// Función para refrescar el token
export async function refreshJwtToken() {
    try {
        // Realiza la solicitud a tu servidor para obtener un nuevo token
        const response = await fetch(`${API_BASE_URL}/refresh-token`, {
            method: "POST",
            credentials: "include", // Usa cookies si es necesario
            headers: {
                "Content-Type": "application/json",
            },
        });

        if (!response.ok) {
            throw new Error("Error al intentar renovar el token");
        }

        const data = await response.json();
        const newToken = data.token; // Asegúrate de que el servidor envíe el nuevo token

        // Almacena el nuevo token en chrome.storage.local
        chrome.storage.local.set({ jwtToken: newToken });

        return newToken;
    } catch (error) {
        console.error("Error al renovar el token:", error);
        return null; // Devuelve null si falla la renovación
    }
}

/**
 * Obtiene el token JWT del almacenamiento local.
 * @returns {Promise<string|null>} - Promesa que resuelve con el token JWT o null.
 */
export function getJwtToken() {
    return new Promise((resolve) => {
        chrome.storage.local.get(["jwtToken"], (result) => {
            const token = result.jwtToken;
            if (!token) {
                console.warn("No se encontró el token JWT en el almacenamiento local");
            } else {
                console.log("Token JWT obtenido del almacenamiento local:", token.substring(0, 20) + "...");
            }
            resolve(token || null);
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
        return {
            success: false,
            error: "No se encontró token de autenticación",
        };
    }

    const authOptions = {
        ...options,
        headers: {
            ...options.headers,
            Authorization: `Bearer ${token}`,
        },
    };

    return fetchFromApi(endpoint, authOptions);
}
