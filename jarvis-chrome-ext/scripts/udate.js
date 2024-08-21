let API;

(async function() {
  const src = chrome.runtime.getURL('api.js');
  API = await import(src);
})();


// Función para iniciar sesión en UDate
async function loginToUdate() {
    try {
        // Obtener los datos almacenados localmente
        const { user, groups, platforms, girls } = await chrome.storage.local.get(['user', 'groups', 'platforms', 'girls']);

        // Verificar si girls está definido y no está vacío
        if (!girls || girls.length === 0) {
            throw new Error('No se encontró información de chicas');
        }

        // Usar la primera chica de la lista
        const girl = girls[0];

        console.log('Usando la siguiente información de chica:', girl);

        // Verificar si estamos en la página correcta de UDate
        const udatePlatform = platforms.find(p => p.name.toLowerCase() === 'udate');
        if (!udatePlatform) {
            throw new Error('No se encontró información de la plataforma UDate');
        }

        const currentUrl = window.location.href;
        if (!currentUrl.includes(udatePlatform.url)) {
            console.log('No estamos en la página de UDate. Navegando a la página de inicio de sesión...');

            // Intentar navegar a la página de inicio de sesión de UDate
            window.location.href = udatePlatform.url;
            // Esperar a que la página cargue
            await new Promise(resolve => setTimeout(resolve, 5000));
        }

        // Verificar si el usuario ya ha iniciado sesión
        const authForm = document.querySelector('.auth-form');
        if (authForm) {
            // El usuario no ha iniciado sesión
            const emailInput = authForm.querySelector('input[type="email"]');
            const passwordInput = authForm.querySelector('input[type="password"]');
            const loginButton = authForm.querySelector('.auth-form__submit');

            if (!emailInput || !passwordInput || !loginButton) {
                throw new Error('No se encontraron los elementos del formulario de inicio de sesión');
            }

            emailInput.value = girl.username;
            passwordInput.value = girl.password;
            loginButton.click();

            // Esperar a que se complete el inicio de sesión
            await new Promise((resolve, reject) => {
                const checkLoginStatus = () => {
                    if (!document.querySelector('.auth-form')) {
                        resolve();
                    } else if (document.querySelector('.error-message')) {
                        reject(new Error('Error de inicio de sesión'));
                    } else {
                        setTimeout(checkLoginStatus, 500);
                    }
                };
                checkLoginStatus();
            });

            console.log('Inicio de sesión exitoso en UDate');
        } else {
            console.log('Ya se ha iniciado sesión en UDate');
        }

        return { success: true, user, groups, platforms, girls };
    } catch (error) {
        console.error('Error en loginToUdate:', error);
        throw error;
    }
}

// Función para realizar Auto Like y Fav
async function autoLikeAndFav() {
    try {
        await loginToUdate();

        console.log('Iniciando Auto Like y Fav');
        const searchLink = document.querySelector('a[href="/search"]');
        if (!searchLink) {
            throw new Error('No se encontró el enlace de búsqueda');
        }
        searchLink.click();

        // Esperar a que los perfiles carguen
        await new Promise(resolve => setTimeout(resolve, 3000));

        const likeButtons = document.querySelectorAll('.like-button');  // Ajusta este selector según la estructura real de la página
        console.log(`Encontrados ${likeButtons.length} botones de like`);

        for (let i = 0; i < likeButtons.length; i++) {
            likeButtons[i].click();
            console.log(`Like dado al perfil ${i + 1}`);

            // Reportar progreso
            chrome.runtime.sendMessage({
                action: 'taskProgress',
                progress: ((i + 1) / likeButtons.length) * 100
            });

            // Esperar un poco entre cada like
            await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));
        }

        const result = `Tarea de Auto Like y Fav completada. Se dieron ${likeButtons.length} likes.`;
        console.log(result);
        return { success: true, message: result };
    } catch (error) {
        console.error('Error en autoLikeAndFav:', error);
        return { success: false, error: error.message };
    }
}

// Otras funciones de tareas (implementa estas según sea necesario)
async function autoFavorite() {
    // Implementa la lógica para auto favoritos
}

async function autoChatMessages() {
    // Implementa la lógica para mensajes de chat automáticos
}

async function autoMailMessages() {
    // Implementa la lógica para mensajes de correo automáticos
}

async function autoMailMessagesWithPhoto() {
    // Implementa la lógica para mensajes de correo con foto automáticos
}

async function verifyIcebreaker() {
    // Implementa la lógica para verificar icebreaker
}

async function activateStories() {
    // Implementa la lógica para activar historias
}
console.log('Content script udate.js está cargando...');

// Envía un mensaje al background script para indicar que el content script está listo
chrome.runtime.sendMessage({action: 'contentScriptReady'})
    .then(response => {
        console.log('contentScriptReady enviado con éxito', response);
    })
    .catch(error => {
        console.error('Error al enviar contentScriptReady:', error);
    });

// Listener para mensajes del background script
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    console.log('Mensaje recibido en udate.js:', request);
    if (request.action === 'executeTask') {
        let taskPromise;
        switch (request.taskName) {
            case 'Auto Like y Fav':
                taskPromise = autoLikeAndFav();
                break;
            case 'Likes Fav Genericos':
                taskPromise = autoFavorite();
                break;
            case 'Mensajes Chat Automaticos':
                taskPromise = autoChatMessages();
                break;
            case 'Mensajes Mail Automaticos':
                taskPromise = autoMailMessages();
                break;
            case 'Mensajes Mail Con Foto Aut.':
                taskPromise = autoMailMessagesWithPhoto();
                break;
            case 'Verficiar Icebreaker':
                taskPromise = verifyIcebreaker();
                break;
            case 'Activar Storie':
                taskPromise = activateStories();
                break;
            default:
                sendResponse({ success: false, error: 'Tarea no reconocida' });
                return false;
        }

        taskPromise.then(result => {
            chrome.runtime.sendMessage({
                action: 'taskComplete',
                result: result
            });
            sendResponse(result);
        }).catch(error => {
            chrome.runtime.sendMessage({
                action: 'taskComplete',
                result: { success: false, error: error.message }
            });
            sendResponse({ success: false, error: error.message });
        });

        return true; // Indica que la respuesta será asíncrona
    }
});

// Exportar funciones que puedan ser necesarias en otros scripts
export {
    loginToUdate,
    autoLikeAndFav,
    autoFavorite,
    autoChatMessages,
    autoMailMessages,
    autoMailMessagesWithPhoto,
    verifyIcebreaker,
    activateStories
};
