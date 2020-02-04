#BoaCompra Magento 2 Module

## BoaCompra official module for Magento 2

This module enable Boa Compra payment method in Magento 2 that allows your store to integrate your checkout with Boa Compra.

## Getting Started

### Prerequisites

- Magento 2.3+
- PHP 7.1+

### Installing

> ATTENTION We recommend that you back up your Magento store prior to any installation or upgrade of the module.

#### Installing via Composer

To install the module, please following commands:

```
composer require boacompra/boacompra-magento2

php bin/magento setup:upgrade

php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy
```

#### Installing via ZIP file

1. Unpack the extension ZIP file on **app/code/Uol/BoaCompra**

2. Run following commands:


```
php bin/magento setup:upgrade

php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy
```

### Configuration

> The sections and paths names in Magento may vary depending on the version and the translation of your store.

To configure the module go to the administrative panel of your store **Stores -> Configuration**. On the configuration screen, choose the **Sales -> Payment Methods** option in the left panel, then look for the **BoaCompra** section, fill in all the required fields.
    - Enabled: Enable or disable the BoaCompra payment Method;
   
   - Environment: Select Sandbox or Production mode for your keys (this information is provided by BoaCompra to you);
   
   - Merchant ID: The Merchat Id key (this information is provided by BoaCompra to you);
   
   - Secret Key: The Secret Key (this information is provided by BoaCompra to you);
   
   - Checkout Type: The checkout type allowed in your store (this information is provided by BoaCompra to you);
   
   - Log Transactions: Enable or disable logging all BoaCompra transactions (it is used by developers).
    
    
## Address (optional)

In order for the user address to be fully sent in some countries through the BoaCompra (like in brazilian address) module the clients address must be configured as follows:

- Access through the administrative panel of your store: **Stores -> Configuration**. On the Configuration screen, access **Customers -> Customer Configuration -> Name and Address Options**
- Change the **Number of Lines in a Street Address** to 4.
    - The BoaCompra Module will wait for the address as follows:
        - address1 = street
        - address2 = number
        - address3 = neighborhood
        - address4 = complement
        
======================================



## Módulo oficial BoaCompra para Magento 2

Este módulo permite o método de pagamento Boa Compra no Magento 2, que permite que sua loja integre seu checkout com a Boa Compra.

## Começando

### Pré-requisitos

- Magento 2.3+
- PHP 7.1 ou superior

### Instalando

> ATENÇÃO Recomendamos que você faça backup da sua loja Magento antes de qualquer instalação ou atualização do módulo.

#### Instalando via Composer

Para instalar o módulo, siga os comandos:


```
composer require boacompra/boacompra-magento2

php bin/magento setup:upgrade

php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy
```

#### Instalando via arquivo ZIP

1. Descompacte o arquivo ZIP da extensão em ** app / code / Uol / BoaCompra **

2. Execute os seguintes comandos:


### Configuração

> Os nomes das seções e caminhos no Magento podem variar dependendo da versão e da tradução da sua loja.

Para configurar o módulo, vá para o painel administrativo da sua loja **Lojas -> Configuração**. Na tela de configuração, escolha a opção **Vendas -> Formas de pagamento** no painel esquerdo e procure a seção **BoaCompra**, preencha todos os campos obrigatórios.

   - Ativado: ativa ou desativa o método de pagamento BoaCompra;
   
   - Ambiente: selecione o modo Sandbox ou Production para suas chaves (essa informação é fornecida pela BoaCompra para você);
   
   - ID do comerciante: a chave do ID do comerciante (essa informação é fornecida pela BoaCompra para você);
   
   - Chave Secreta: A Chave Secreta (esta informação é fornecida pela BoaCompra para você);
   
   - Tipo de finalização da compra: o tipo de finalização da compra permitido em sua loja (essa informação é fornecida pela BoaCompra para você);
   
   - Transações de log: ative ou desative o log de todas as transações do BoaCompra (usadas pelos desenvolvedores).

## Endereço (opcional)

Para que o endereço do usuário seja totalmente enviado em alguns países através do módulo BoaCompra (como no endereço brasileiro), o endereço do cliente deve ser configurado da seguinte maneira:

- Acesse através do painel administrativo da sua loja: **Lojas -> Configuração**. Na tela Configuração, acesse **Clientes -> Configuração do cliente -> Opções de nome e endereço**
- Altere o * Número de linhas em um endereço** para 4.
     - O módulo BoaCompra aguardará o endereço da seguinte forma:
         - endereço1 = rua
         - endereço2 = número
         - address3 = bairro
         - endereço4 = complemento