console.log('Content script loaded');

chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
  if (request.action === 'performTask') {
    console.log('Performing task:', request.taskName);
    // Aquí iría la lógica para realizar la tarea
    sendResponse({success: true, message: 'Task completed'});
  }
});
