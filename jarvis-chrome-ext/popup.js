import API from '/api.js';
import * as UDate from './scripts/udate.js';
import * as TalkyTimes from './scripts/talkytimes.js';
import * as GroveSecret from './scripts/grovesecret.js';
import * as AmoLatina from './scripts/amolatina.js';

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const userInfo = document.getElementById('userInfo');
    const taskButtons = document.getElementById('taskButtons');
    const status = document.getElementById('status');
    const loggedInUser = document.getElementById('loggedInUser');
    const userGroups = document.getElementById('userGroups');
    const userPlatforms = document.getElementById('userPlatforms');
    const loginButton = document.getElementById('loginButton');
    const logoutButton = document.getElementById('logoutButton');

    function updateUI(isLoggedIn, userData = null) {
        loginForm.style.display = isLoggedIn ? 'none' : 'block';
        userInfo.style.display = isLoggedIn ? 'block' : 'none';
        taskButtons.style.display = isLoggedIn ? 'block' : 'none';

        if (isLoggedIn && userData) {
            loggedInUser.textContent = userData.name;

            if (userGroups && userData.groups) {
                userGroups.innerHTML = '<strong>Grupo:</strong> ' + userData.groups.map(g => `${g.name} <br/> Chicas Asignadas: (${g.girls_count})`).join(', ');
            } else {
                console.warn('Element with id "userGroups" not found in the DOM or userData.groups is undefined');
            }

            if (userPlatforms && userData.platforms) {
                userPlatforms.innerHTML = '<strong>Platforma:</strong> ' + userData.platforms.map(p => p.name).join(', ');
            } else {
                console.warn('Element with id "userPlatforms" not found in the DOM or userData.platforms is undefined');
            }

            loadTasks();
        }
    }

    async function checkLoginStatus() {
        const { token, user, groups, platforms } = await chrome.storage.local.get(['token', 'user', 'groups', 'platforms']);
        if (token && user) {
            updateUI(true, { ...user, groups, platforms });
        }
    }

    checkLoginStatus();

    loginButton.addEventListener('click', async function() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        status.textContent = 'Ingresando...';
        const result = await API.login(email, password);

        if (result.success) {
            updateUI(true, result.user);
            status.textContent = 'Ingreso exitoso';
            chrome.runtime.sendMessage({ action: 'startTokenRefresh' });
        } else {
            status.textContent = 'Error Ingreso: ' + result.error;
        }
    });

    logoutButton.addEventListener('click', async function() {
        const result = await API.logout();
        if (result.success) {
            updateUI(false);
            status.textContent = 'Saliste Correctamente';
            chrome.runtime.sendMessage({ action: 'stopTokenRefresh' });
        } else {
            status.textContent = 'Error: ' + result.error;
        }
    });

    function loadTasks() {
        chrome.tabs.query({active: true, currentWindow: true}, async function(tabs) {
            const currentUrl = tabs[0].url;
            let tasks;

            if (currentUrl.includes('udate.love')) {
                tasks = [
                    { name: 'Auto Like y Fav', action: UDate.autoLikeAndFav },
                    { name: 'Likes Fav Genericos', action: UDate.autoFavorite },
                    { name: 'Mensajes Chat Automaticos', action: UDate.autoChatMessages },
                    { name: 'Mensajes Mail Automaticos', action: UDate.autoMailMessages },
                    { name: 'Mensajes Mail Con Foto Aut.', action: UDate.autoMailMessagesWithPhoto },
                    { name: 'Verficiar Icebreaker', action: UDate.verifyIcebreaker },
                    { name: 'Activar Storie', action: UDate.activateStories }
                ];
            } else if (currentUrl.includes('talkytimes.com') || currentUrl.includes('allcreate.com')) {
                tasks = [
                    { name: 'Auto Favorito', action: TalkyTimes.autoFavorite },
                    { name: 'Auto Chat Messages', action: TalkyTimes.autoChatMessages }
                ];
            } else if (currentUrl.includes('grovesecret.com')) {
                tasks = [
                    { name: 'Auto Favorite', action: GroveSecret.autoFavorite },
                    { name: 'Auto Chat Messages', action: GroveSecret.autoChatMessages }
                ];
            } else if (currentUrl.includes('amolatina.com')) {
                tasks = [
                    { name: 'Auto Chat Messages', action: AmoLatina.autoChatMessages }
                ];
            }

            if (tasks) {
                taskButtons.innerHTML = '';
                tasks.forEach(task => {
                    const button = document.createElement('button');
                    button.textContent = task.name;
                    button.className = 'task-button';
                    button.addEventListener('click', async function() {
                        await startTask(task.name, getCurrentPlatform(currentUrl), tabs[0].id);
                    });
                    taskButtons.appendChild(button);
                });
            } else {
                taskButtons.innerHTML = '<p>No hay tareas disponibles para esta página.</p>';
            }
        });
    }

    async function startTask(taskName, platform, tabId) {
        status.textContent = `Iniciando ${taskName}...`;
        try {
            if (!platform || platform === 'Unknown') {
                throw new Error('Esta tarea solo se puede ejecutar en una página de plataforma soportada');
            }

            const result = await chrome.runtime.sendMessage({
                action: 'startTask',
                taskName: taskName,
                platform: platform,
                tabId: tabId
            });

            console.log('Respuesta del background script:', result);
            if (result && typeof result.success === 'boolean') {
                if (result.success) {
                    status.textContent = `${taskName} iniciada. ${result.message || ''}`;
                } else {
                    status.textContent = `Error al iniciar ${taskName}: ${result.error || 'Error desconocido'}`;
                }
            } else {
                throw new Error('Respuesta inesperada del background script');
            }
        } catch (error) {
            console.error(`Error al iniciar tarea ${taskName}:`, error);
            status.textContent = `Error al iniciar ${taskName}: ${error.message}`;
        }
    }

    function getCurrentPlatform(url) {
        if (url.includes('udate.love')) return 'UDate';
        if (url.includes('talkytimes.com') || url.includes('allcreate.com')) return 'TalkyTimes';
        if (url.includes('grovesecret.com')) return 'GroveSecret';
        if (url.includes('amolatina.com')) return 'AmoLatina';
        return 'Unknown';
    }

    chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
        console.log('Mensaje recibido en popup:', message);
        if (message.action === 'taskProgress') {
            status.textContent = `Progreso de ${message.taskName}: ${message.progress.toFixed(2)}%`;
        } else if (message.action === 'taskComplete') {
            if (message.result.success) {
                status.textContent = `Tarea ${message.taskName} completada: ${message.result.message}`;
            } else {
                status.textContent = `Error en tarea ${message.taskName}: ${message.result.error}`;
            }
        }
    });
});
