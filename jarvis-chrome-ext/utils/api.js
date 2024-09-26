// api.js

let API_BASE_URL = "https://jarvisbot.biz/api";

export function setApiBaseUrl(url) {
    API_BASE_URL = url;
    console.log(`API base URL configurada a: ${API_BASE_URL}`);
}

export function getJwtTokenFromCookie() {
    return new Promise((resolve) => {
        chrome.cookies.get(
            { url: "https://jarvisbot.biz", name: "jwt_token" },
            (cookie) => {
                if (cookie) {
                    resolve(cookie.value);
                } else {
                    resolve(null);
                }
            }
        );
    });
}

export async function getJwtToken() {
    let token = await new Promise((resolve) => {
        chrome.storage.local.get("jwtToken", (data) => {
            resolve(data.jwtToken || null);
        });
    });

    if (!token) {
        token = await getJwtTokenFromCookie();
        if (token) {
            await chrome.storage.local.set({ jwtToken: token });
        }
    }

    return token;
}

export async function fetchFromApi(endpoint, options = {}) {
    try {
        const url = `${API_BASE_URL}${endpoint}`;
        const token = await getJwtToken();

        if (!token) {
            throw new Error("No se encontró token JWT");
        }

        const response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
            },
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
    const token = await getJwtToken();
    if (!token) {
        console.error("No se encontró token JWT");
        throw new Error("No autorizado");
    }

    return fetchFromApi(endpoint, {
        ...options,
        headers: {
            ...options.headers,
            Authorization: `Bearer ${token}`,
        },
    });
}

export async function clearUserSession() {
    console.log("Limpiando sesión del usuario");
    await chrome.storage.local.remove(["userInfo", "jwtToken"]);
    try {
        await chrome.runtime.sendMessage({ action: "sessionCleared" });
    } catch (error) {
        console.warn("No se pudo enviar el mensaje de sesión limpiada:", error);
    }
    console.log("Sesión del usuario limpiada");
}

export async function refreshToken() {
    try {
        const response = await fetch(`${API_BASE_URL}/auth/refresh`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            credentials: "include",
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        if (data.success && data.token) {
            return data.token;
        } else {
            throw new Error("No se recibió un nuevo token");
        }
    } catch (error) {
        console.error("Error al refrescar el token:", error);
        throw error;
    }
}
export async function checkAndRefreshToken() {
    try {
        const token = await getJwtToken();
        if (!token) {
            console.log("No hay token, intentando obtener de la cookie");
            const cookieToken = await getJwtTokenFromCookie();
            if (cookieToken) {
                await chrome.storage.local.set({ jwtToken: cookieToken });
                return cookieToken;
            }
            throw new Error("No se encontró token JWT");
        }

        // Intenta validar el token
        await fetchFromApi("/auth/validate-token");
        return token; // Si no hay error, el token es válido
    } catch (error) {
        console.log("Error al validar token, intentando refrescar");
        try {
            const newToken = await refreshToken();
            await chrome.storage.local.set({ jwtToken: newToken });
            return newToken;
        } catch (refreshError) {
            console.error("Error al refrescar token:", refreshError);
            await clearUserSession();
            throw new Error("No se pudo refrescar el token");
        }
    }
}
