// content.js

// Objeto global para almacenar las funciones de las plataformas
window.platformFunctions = {};

// Establecer conexión con el background script
const port = chrome.runtime.connect({name: "contentScriptConnection"});

// Inicialización del content script
console.log('Content script iniciado');

// Función para cargar dinámicamente el script de la plataforma actual
function loadPlatformScript(platform) {
  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = chrome.runtime.getURL(`platforms/${platform}.js`);
    script.onload = () => {
      console.log(`Script de plataforma ${platform} cargado`);
      resolve();
    };
    script.onerror = () => reject(new Error(`Error al cargar el script de ${platform}`));
    (document.head || document.documentElement).appendChild(script);
  });
}

// Función para inicializar las funciones de la plataforma
async function initializePlatform(platform, userInfo) {
  try {
    await loadPlatformScript(platform);
    if (window[`${platform}Tasks`]) {
      window.platformFunctions = window[`${platform}Tasks`];
      console.log(`Funciones de ${platform} inicializadas`);
      // Inicializar la plataforma con la información del usuario si es necesario
      if (window.platformFunctions.init) {
        await window.platformFunctions.init(userInfo);
      }
    } else {
      console.error(`No se encontraron funciones para ${platform}`);
    }
  } catch (error) {
    console.error(`Error al inicializar ${platform}:`, error);
    throw error;
  }
}

// Función para ejecutar una tarea
async function executeTask(task) {
  if (window.platformFunctions[task.name]) {
    try {
      console.log(`Ejecutando tarea: ${task.name}`);
      const result = await window.platformFunctions[task.name](task.params);
      port.postMessage({
        action: 'taskCompleted',
        taskName: task.name,
        result: result
      });
      return result;
    } catch (error) {
      console.error(`Error al ejecutar la tarea ${task.name}:`, error);
      port.postMessage({
        action: 'taskFailed',
        taskName: task.name,
        error: error.message
      });
      throw error;
    }
  } else {
    console.error(`Tarea ${task.name} no encontrada`);
    throw new Error(`Tarea ${task.name} no encontrada`);
  }
}

// Listener para mensajes del background script a través del puerto
port.onMessage.addListener((message) => {
  console.log('Mensaje recibido en content script:', message);
  switch (message.action) {
    case 'initPlatform':
      initializePlatform(message.platform, message.userInfo)
        .then(() => port.postMessage({ action: 'platformInitialized', success: true }))
        .catch((error) => port.postMessage({ action: 'platformInitialized', success: false, error: error.message }));
      break;

    case 'executeTask':
      executeTask(message.task)
        .then((result) => port.postMessage({ action: 'taskCompleted', success: true, result: result }))
        .catch((error) => port.postMessage({ action: 'taskFailed', success: false, error: error.message }));
      break;

    default:
      console.log('Acción no reconocida:', message.action);
      port.postMessage({ action: 'error', error: 'Acción no reconocida' });
  }
});

// Función para inyectar un script en la página
function injectScript(file) {
  const script = document.createElement('script');
  script.setAttribute('type', 'text/javascript');
  script.setAttribute('src', chrome.runtime.getURL(file));
  document.body.appendChild(script);
}

// Inyectar script de utilidades para interacción con el DOM
injectScript('utils/domInteractions.js');

// Listener para mensajes desde scripts inyectados
window.addEventListener('message', function(event) {
  // Asegurarse de que el mensaje viene de la misma ventana
  if (event.source !== window) return;

  if (event.data.type && event.data.type === 'FROM_PAGE_SCRIPT') {
    console.log('Mensaje recibido del script de página:', event.data.payload);
    // Enviar el mensaje al background script
    port.postMessage({
      action: 'pageScriptMessage',
      payload: event.data.payload
    });
  }
}, false);

// Notificar al background script que el content script está listo
port.postMessage({ action: 'contentScriptReady' });
console.log('Content script notificó que está listo');

// Manejar la desconexión del puerto
port.onDisconnect.addListener(() => {
  console.log('Conexión con el background script perdida');
  // Aquí puedes manejar la reconexión si es necesario
});
