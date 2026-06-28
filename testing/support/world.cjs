const { setWorldConstructor } = require('@cucumber/cucumber');

class FunctionalWorld {
  constructor() {
    this.baseUrl = process.env.BASE_URL || 'http://127.0.0.1:8080/tiendaAbarrotes';
    this.browser = null;
    this.context = null;
    this.page = null;
    this.lastDataTable = null;
  }
}

setWorldConstructor(FunctionalWorld);
