// popup.js

document.addEventListener("DOMContentLoaded", function () {
    const userInfoDiv = document.getElementById("userInfo");
    const taskListDiv = document.getElementById("taskList");
    const statusDiv = document.getElementById("status");
    const refreshButton = document.getElementById("refreshSession");

    // Cargar información del usuario
    function loadUserInfo() {
        chrome.storage.local.get(["userInfo", "operatorInfo"], function (data) {
            if (data.userInfo && data.operatorInfo) {
                displayUserInfo(data.userInfo, data.operatorInfo);
                setupTaskButtons();
            } else {
                displayLoginForm();
            }
        });
    }

    // Mostrar información del usuario
    function displayUserInfo(userInfo) {
        userInfoDiv.innerHTML = `
            <h2>Información del Usuario</h2>
            <p>Nombre: ${userInfo.name}</p>
            <p>Plataforma: ${userInfo.platform}</p>
            <h3>Grupos:</h3>
            <ul>
                ${userInfo.groups
                    .map(
                        (group) => `
                    <li>
                        ${group.name} (${group.girls_count} chicas)
                        <ul>
                            ${group.girls
                                .map(
                                    (girl) => `
                                <li>${girl.username} - ${girl.platform}</li>
                            `
                                )
                                .join("")}
                        </ul>
                    </li>
                `
                    )
                    .join("")}
            </ul>
        `;
    }

    // Cargar tareas disponibles
    function loadTasks(platform) {
        chrome.runtime.sendMessage(
            { action: "getPlatformTasks", platform: platform },
            function (response) {
                if (response.tasks && response.tasks.length > 0) {
                    taskListDiv.innerHTML = "<h2>Tareas Disponibles:</h2>";
                    response.tasks.forEach((task) => {
                        const button = document.createElement("button");
                        button.textContent = task;
                        button.onclick = () => executeTask(platform, task);
                        taskListDiv.appendChild(button);
                    });
                } else {
                    taskListDiv.innerHTML =
                        "<p>No hay tareas disponibles para esta plataforma.</p>";
                }
            }
        );
    }

    // Ejecutar tarea
    function executeTask(platform, taskName) {
        statusDiv.textContent = `Ejecutando tarea: ${taskName}...`;
        chrome.runtime.sendMessage(
            {
                action: "executeTask",
                task: { platform: platform, name: taskName },
            },
            function (response) {
                if (response.success) {
                    statusDiv.textContent = `Tarea ${taskName} añadida a la cola.`;
                } else {
                    statusDiv.textContent = `Error al añadir tarea: ${response.error}`;
                }
            }
        );
    }

    // Actualizar sesión
    refreshButton.addEventListener("click", function () {
        statusDiv.textContent = "Actualizando sesión...";
        chrome.runtime.sendMessage(
            { action: "checkSession" },
            function (response) {
                statusDiv.textContent = "Sesión actualizada.";
                loadUserInfo();
            }
        );
    });

    // Listener para actualizaciones de sesión
    chrome.runtime.onMessage.addListener(function (
        request,
        sender,
        sendResponse
    ) {
        if (request.action === "sessionUpdated") {
            loadUserInfo();
        } else if (request.action === "sessionCleared") {
            userInfoDiv.textContent =
                "Sesión cerrada. Por favor, inicia sesión en JarvisBot.";
            taskListDiv.innerHTML = "";
        }
    });

    // Cargar información inicial
    loadUserInfo();
});
