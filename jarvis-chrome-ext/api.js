const API_BASE_URL = ' http://54.184.25.165/api';

class API {
    static async request(endpoint, method = 'GET', body = null) {
        const token = await chrome.storage.local.get('token').then(data => data.token);

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            method: method,
            headers: headers,
            credentials: 'include'
        };

        if (body) {
            config.body = JSON.stringify(body);
        }

        try {
            console.log(`Sending request to ${API_BASE_URL}${endpoint}`, config);
            const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
            const responseData = await response.json();

            if (!response.ok) {
                console.error('API error:', responseData);
                throw new Error(responseData.error || `HTTP error! status: ${response.status}`);
            }

            console.log('API response:', responseData);
            return responseData;
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }

    static async login(email, password) {
        try {
            console.log('Login attempt', { email, password: '****' });
            const data = await this.request('/auth/login', 'POST', { email, password });
            await chrome.storage.local.set({
                token: data.access_token,
                user: data.user,
                groups: data.user.groups,
                platforms: data.user.platforms
            });

            // Cargar información adicional de plataformas y chicas
            await this.loadPlatformsAndGirls();

            console.log('Login successful', data.user);
            return {
                success: true,
                user: data.user,
                groups: data.user.groups,
                platforms: data.user.platforms
            };
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: error.message };
        }
    }
    static async loadPlatformsAndGirls() {
        try {
            const platformsData = await this.request('/platforms', 'GET');
            const girlsData = await this.request('/girls', 'GET');

            const platforms = platformsData.map(platform => ({
                name: platform.name,
                accessMode: platform.access_mode
            }));

            const girls = girlsData.map(girl => ({
                name: girl.name,
                internalId: girl.internal_id,
                username: girl.username,
                password: girl.password,
                platform: girl.platform
            }));

            await chrome.storage.local.set({ platforms, girls });
            console.log('Platforms and girls data loaded successfully');
        } catch (error) {
            console.error('Error loading platforms and girls data:', error);
            throw error;
        }
    }
    static async refreshToken() {
        try {
            const token = await chrome.storage.local.get('token').then(data => data.token);
            if (!token) {
                throw new Error('No token found');
            }

            const data = await this.request('/auth/refresh', 'POST', { token });
            await chrome.storage.local.set({ token: data.access_token });

            console.log('Token refreshed successfully');
            return { success: true };
        } catch (error) {
            console.error('Token refresh error:', error);
            return { success: false, error: error.message };
        }
    }

    static async logout() {
        try {
            await this.request('/auth/logout', 'POST');
            await chrome.storage.local.remove(['token', 'user', 'groups', 'platforms']);
            return { success: true };
        } catch (error) {
            console.error('Logout error:', error);
            return { success: false, error: error.message };
        }
    }

    static async getUserData() {
        try {
            const data = await this.request('/auth/me', 'GET');
            await chrome.storage.local.set({ user: data });
            return { success: true, user: data };
        } catch (error) {
            console.error('Get user data error:', error);
            return { success: false, error: error.message };
        }
    }

    // Estos métodos pueden no ser necesarios si obtienes toda la información en el login
    // Pero los mantendremos por si necesitas actualizaciones en tiempo real

    static async getGirls() {
        // Este método ahora devuelve los datos almacenados localmente
        try {
            const data = await chrome.storage.local.get('girls');
            return { success: true, girls: data.girls };
        } catch (error) {
            console.error('Get girls error:', error);
            return { success: false, error: error.message };
        }
    }


    static async getGroups() {
        try {
            const data = await this.request('/groups', 'GET');
            await chrome.storage.local.set({ groups: data });
            return { success: true, groups: data };
        } catch (error) {
            console.error('Get groups error:', error);
            return { success: false, error: error.message };
        }
    }

    static async getPlatforms() {
        // Este método ahora devuelve los datos almacenados localmente
        try {
            const data = await chrome.storage.local.get('platforms');
            return { success: true, platforms: data.platforms };
        } catch (error) {
            console.error('Get platforms error:', error);
            return { success: false, error: error.message };
        }
    }
    static async saveTaskResult(taskName, platformName, result) {
        try {
            const response = await this.request('/task-results', 'POST', {
                task_name: taskName,
                platform_name: platformName,
                result: result
            });
            console.log('Task result saved successfully:', response);
            return { success: true, data: response };
        } catch (error) {
            console.error('Error saving task result:', error);
            return { success: false, error: error.message };
        }
    }
}

export default API;
