import time
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
import os
from dotenv import load_dotenv
from plyer import notification
import requests

# Cargar variables de entorno
load_dotenv()

def setup_driver():
    chrome_options = Options()
    chrome_options.add_argument("user-data-dir=./chrome_profile")
    service = Service(ChromeDriverManager().install())
    return webdriver.Chrome(service=service, options=chrome_options)

def check_active_session(driver):
    try:
        driver.get("https://jarvisbot.biz")
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.TAG_NAME, "body")))
        
        cookies = driver.get_cookies()
        for cookie in cookies:
            if cookie['name'] == 'sanctum_token' and cookie['value']:
                return cookie['value']
        return None
    except Exception as e:
        print(f"Error al verificar la sesión: {e}")
        return None

def get_user_info(token):
    try:
        # Asumiendo que tienes un endpoint para obtener la información del usuario
        response = requests.get('https://jarvisbot.biz/api/user', headers={
            'Authorization': f'Bearer {token}'
        })
        if response.status_code == 200:
            return response.json()
        else:
            print(f"Error al obtener información del usuario: {response.status_code}")
            return None
    except Exception as e:
        print(f"Error al hacer la solicitud de información del usuario: {e}")
        return None

def send_notification(title, message):
    notification.notify(
        title=title,
        message=message,
        app_icon=None,
        timeout=10,
    )

def wait_for_active_session(driver):
    print("Esperando una sesión activa...")
    while True:
        token = check_active_session(driver)
        if token:
            user_info = get_user_info(token)
            if user_info:
                send_notification("Sesión Iniciada", f"Se ha detectado una sesión activa para el usuario: {user_info['name']}")
                print(f"Sesión activa detectada para el usuario: {user_info['name']}")
                return token, user_info
        time.sleep(30)

def perform_task(driver, task, user_info):
    try:
        if task == 'example_task':
            example_task(driver, user_info)
        # Agrega más tareas según sea necesario
    except Exception as e:
        print(f"Error al realizar la tarea {task}: {e}")

def example_task(driver, user_info):
    print(f"Realizando tarea de ejemplo para el usuario: {user_info['name']}")
    # Implementa la lógica de la tarea aquí

def main():
    print("Iniciando el script de monitoreo...")
    driver = setup_driver()
    
    try:
        while True:
            token, user_info = wait_for_active_session(driver)
            
            while check_active_session(driver) == token:
                perform_task(driver, 'example_task', user_info)
                time.sleep(60)  # Espera 1 minuto antes de verificar nuevamente
            
            send_notification("Sesión Cerrada", f"La sesión de {user_info['name']} en jarvisbot.biz se ha cerrado")
            print(f"La sesión de {user_info['name']} se ha cerrado. Esperando nueva sesión...")
    finally:
        driver.quit()

if __name__ == "__main__":
    main()