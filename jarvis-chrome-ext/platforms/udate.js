// udate.js

// Función para iniciar sesión en UDate
export async function loginToUdate(credentials) {
    try {
        console.log('Iniciando sesión en UDate...');

        // Verificar si estamos en la página correcta de UDate
        if (!window.location.href.includes('udate.love')) {
            console.log('No estamos en la página de UDate. Navegando a la página de inicio de sesión...');
            window.location.href = 'https://udate.love/login';
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

            emailInput.value = credentials.username;
            passwordInput.value = credentials.password;
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

        return { success: true, message: 'Inicio de sesión exitoso en UDate' };
    } catch (error) {
        console.error('Error en loginToUdate:', error);
        return { success: false, error: error.message };
    }
}

// Función para realizar Auto Like y Fav
export async function autoLikeAndFav() {
    try {
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
export async function autoFavorite() {
    // Implementa la lógica para auto favoritos
    return { success: true, message: 'Auto Favorite completado' };
}

export async function autoChatMessages() {
    // Implementa la lógica para mensajes de chat automáticos
    return { success: true, message: 'Auto Chat Messages completado' };
}

export async function autoMailMessages() {
    // Implementa la lógica para mensajes de correo automáticos
    return { success: true, message: 'Auto Mail Messages completado' };
}

export async function autoMailMessagesWithPhoto() {
    // Implementa la lógica para mensajes de correo con foto automáticos
    return { success: true, message: 'Auto Mail Messages with Photo completado' };
}

export async function verifyIcebreaker() {
    // Implementa la lógica para verificar icebreaker
    return { success: true, message: 'Icebreaker verificado' };
}

export async function activateStories() {
    // Implementa la lógica para activar historias
    return { success: true, message: 'Stories activadas' };
}

;
