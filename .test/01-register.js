// carreguem les llibreries
const { BaseTest } = require("./BasePhpTest.js")
const {Builder, By, Key, until, Select} = require('selenium-webdriver');
const assert = require('assert');

// heredem una classe amb un sol mètode test()
// emprem this.driver per utilitzar Selenium

class MyTest extends BaseTest
{
	async test() {
                // Inicialitzar servidor PHP i anar a la pagina register
                await this.driver.get("http://0.0.0.0:8080");
                var register_button = await this.driver.findElement(By.css("a[href='register.php']"));
                await this.driver.actions()
                .move({ origin: register_button })
                .click()
                .perform()

                // Comprova que existeixen els inputs de register i inserta les dades corresponents
                let input_username = await this.driver.wait(until.elementLocated(By.id("userName"), 20))
                assert(input_username, "ERROR TEST: input 'userName' no trobat")
                await input_username.sendKeys("Selenium Tester");
                await input_username.sendKeys(Key.TAB);

                let input_password = await this.driver.wait(until.elementLocated(By.id("password"), 20))
                assert(input_password, "ERROR TEST: input 'password' no trobat")
                await input_password.sendKeys("Asdasd123_");
                await input_password.sendKeys(Key.ENTER);

                let input_password_confirm = await this.driver.wait(until.elementLocated(By.id("confirmPassword"), 20))
                assert(input_password_confirm, "ERROR TEST: input 'confirmPassword' no trobat")
                await input_password_confirm.sendKeys("Asdasd123_");
                await input_password.sendKeys(Key.ENTER);

                let input_email = await this.driver.wait(until.elementLocated(By.id("email")), 20)
                assert(input_email, "ERROR TEST: input 'email' no trobat")
                await input_email.sendKeys("selenium@test.com")
                await input_email.sendKeys(Key.ENTER)

                let input_country = new Select(await this.driver.wait(until.elementLocated(By.id("country"))), 20)
                assert(input_country, "ERROR TEST: select 'country' no trobat")
                await input_country.selectByVisibleText("Spain")
                await input_email.sendKeys(Key.ENTER)

                let input_city = await this.driver.wait(until.elementLocated(By.id("city")), 20)
                assert(input_city, "ERROR TEST: input 'city' no trobat")
                await input_city.sendKeys("Barcelona")
                await input_city.sendKeys(Key.ENTER)

                let input_postalCode = await this.driver.wait(until.elementLocated(By.id("postalCode")), 20)
                assert(input_postalCode, "ERROR TEST: input 'postal code' no trobat")
                await input_postalCode.sendKeys("08800")
                await input_postalCode.sendKeys(Key.ENTER)

                let input_phone = await this.driver.wait(until.elementLocated(By.id("mobile")), 20)
                assert(input_phone, "ERROR TEST: input 'mobile' no trobat")
                await input_phone.sendKeys("123456789")
                await input_phone.sendKeys(Key.ENTER)

                // Fa el submit i busca el popup de registre completat al recarregar la pagina
                let submit = await this.driver.wait(until.elementLocated(By.id("submit")), 20)
                assert(submit, "ERROR TEST: botó submit no trobat")
                await submit.sendKeys(Key.ENTER)

                await new Promise(resolve => setTimeout(resolve, 2000));

                // Obtener la URL actual
                const currentUrl = await this.driver.getCurrentUrl();

                // URL deseada
                const desiredUrl = 'http://0.0.0.0:8080/validate_email.php';

                // Normalizar las URLs para una comparación más precisa
                const normalizeUrl = (url) => url.toLowerCase().trim().replace(/\/$/, '');

                // Comprobar si las URLs son iguales después de la normalización
                if (normalizeUrl(currentUrl) === normalizeUrl(desiredUrl)) {
                        console.log("TEST OK");
                } else {
                        throw new Error("ERROR TEST: Register no completado. La URL actual no coincide con la URL deseada.");
                }
        }
}
// executem el test
(async function test_example() {
	const test = new MyTest();
	await test.run();
	console.log("END")
})();