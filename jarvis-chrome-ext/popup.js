document.addEventListener('DOMContentLoaded', function() {
    chrome.storage.local.get(['userInfo'], function(result) {
        if (result.userInfo) {
            displayUserInfo(result.userInfo);
        } else {
            displayLoginPrompt();
        }
    });
});
function displayUserData(userData) {
    const userDataDiv = document.getElementById('userData');
    userDataDiv.innerHTML = `
        <h2>Información del Usuario</h2>
        <p><strong>Nombre:</strong> ${userData.name}</p>
        <p><strong>Email:</strong> ${userData.email}</p>
        <p><strong>Rol:</strong> ${userData.role}</p>
        <h3>Grupos:</h3>
        <ul>
            ${userData.groups.map(group => `
                <li>
                    <strong>${group.name}</strong> (Turno: ${group.shift})
                    <ul>
                        ${group.girls.map(girl => `
                            <li>${girl.name} (${girl.platform})</li>
                        `).join('')}
                    </ul>
                </li>
            `).join('')}
        </ul>
        <h3>Plataformas:</h3>
        <ul>
            ${userData.platforms.map(platform => `<li>${platform}</li>`).join('')}
        </ul>
    `;

    // Detectar la plataforma actual y crear los botones de tareas
    chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
        const url = tabs[0].url;
        const platform = detectPlatform(url);
        if (platform) {
            createTaskButtons(platform);
        }
    });
}
function displayLoginPrompt() {
    const userInfoDiv = document.getElementById('userInfo');
    userInfoDiv.innerHTML = `
        <p>Por favor, inicia sesión en <a href="https://jarvisbot.biz" target="_blank">JarvisBot</a> para usar la extensión.</p>
    `;
}
function displayError(message) {
    const userDataDiv = document.getElementById('userData');
    userDataDiv.innerHTML = `<p class="error">${message}</p>`;
}

function createTaskButtons(platform) {
    const taskButtonsDiv = document.getElementById('taskButtons');
    taskButtonsDiv.innerHTML = '<h3>Tareas Automáticas</h3>';

    const tasks = getTasksForPlatform(platform);
    tasks.forEach(task => {
        const button = document.createElement('button');
        button.textContent = task.name;
        button.onclick = () => executeTask(task.id);
        taskButtonsDiv.appendChild(button);
    });
}

function getTasksForPlatform(platform) {
    const tasks = {
        udate: [
            { id: 'sendMessage', name: 'Enviar Mensaje' },
            { id: 'updateProfile', name: 'Actualizar Perfil' }
        ],
        talkytimes: [
            { id: 'sendLike', name: 'Enviar Like' },
            { id: 'browseProfiles', name: 'Explorar Perfiles' }
        ],
        amolatina: [
            { id: 'sendGift', name: 'Enviar Regalo' },
            { id: 'startChat', name: 'Iniciar Chat' }
        ]
    };
    return tasks[platform] || [];
}

function executeTask(taskId) {
    chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
        chrome.tabs.sendMessage(tabs[0].id, {action: "executeTask", taskId: taskId});
    });
}

function detectPlatform(url) {
    if (url.includes("udate.love")) return "udate";
    if (url.includes("talkytimes.com")) return "talkytimes";
    if (url.includes("amolatina.com")) return "amolatina";
    return null;
}