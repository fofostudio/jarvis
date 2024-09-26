// popup.js

document.addEventListener('DOMContentLoaded', async function() {
    const userInfo = await chrome.runtime.sendMessage({ action: 'getUserInfo' });
    const userInfoDiv = document.getElementById('userInfo');
    const girlSelect = document.getElementById('girlSelect');
    const taskButtons = document.getElementById('taskButtons');

    if (userInfo && userInfo.userInfo) {
        displayUserInfo(userInfo.userInfo);
        populateGirlSelect(userInfo.userInfo);

        girlSelect.addEventListener('change', () => loadTasks(userInfo.userInfo));

        const activeTab = await getCurrentTab();
        const activePlatform = detectPlatform(activeTab.url);
        if (activePlatform) {
            loadTasks(userInfo.userInfo, activePlatform);
        }
    } else {
        userInfoDiv.innerHTML = `<p>No has iniciado sesión. Por favor, inicia sesión en el sitio web de JarvisBot.</p>`;
    }
});

function displayUserInfo(user) {
    const userInfoDiv = document.getElementById('userInfo');
    userInfoDiv.innerHTML = `
        <h2>Bienvenido, ${user.name}</h2>
        <p>Email: ${user.email}</p>
        <p>Rol: ${user.role}</p>
    `;
}

function populateGirlSelect(userInfo) {
    const girlSelect = document.getElementById('girlSelect');
    const allGirls = userInfo.groups.flatMap(group => group.girls);
    girlSelect.innerHTML = allGirls.map(girl => `
        <option value="${girl.id}">${girl.name} (${girl.platform})</option>
    `).join('');
}

async function loadTasks(userInfo, platform) {
    const taskButtons = document.getElementById('taskButtons');
    const girlId = document.getElementById('girlSelect').value;
    const girl = userInfo.groups.flatMap(group => group.girls).find(g => g.id.toString() === girlId);

    if (!platform && girl) {
        platform = girl.platform;
    }

    const tasks = await getTasks(platform);

    taskButtons.innerHTML = tasks.map(task => `
        <button
            onclick="executeTask('${platform}', '${task}', ${JSON.stringify(girl)})"
            ${girl && girl.platform === platform ? '' : 'disabled'}
        >
            ${task}
        </button>
    `).join('');
}

function executeTask(platform, task, girlData) {
    chrome.runtime.sendMessage({ action: 'executeTask', platform, task, girlData });
}

async function getCurrentTab() {
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
    return tab;
}

function detectPlatform(url) {
    const platforms = ['udate', 'talkytimes', 'amolatina'];
    return platforms.find(platform => url.includes(platform)) || null;
}

async function getTasks(platform) {
    // En una implementación real, deberías obtener esto del backend
    const tasks = {
        UDate: ['Login', 'Auto Like', 'Send Message'],
        TalkyTimes: ['Login', 'Update Profile', 'Check Messages'],
        AmoLatina: ['Login', 'Browse Profiles', 'Send Gift']
    };
    return tasks[platform] || [];
}

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.action === 'taskCompleted') {
        alert(`Tarea "${message.task}" completada en ${message.platform}`);
    } else if (message.action === 'taskFailed') {
        alert(`Error al ejecutar la tarea "${message.task}" en ${message.platform}: ${message.error}`);
    }
});
