# Acerca del Proyecto

Este es un proyecto realizado en PHP utilizando Laravel. 
Implementa la librería Transbank WebServices SDK para la integración de Webpay Plus y Webpay OneClick. 
Los creditos por esta libreríapertenecen a **Gonzalo De Spirito** de freshworkstudio.com y simplepay.cl.

###### Requerimientos
Instalar Laravel
https://laravel.com/docs/5.4

###### Índice
- [OneClick](#oneclick)
    1. [Inscripción](#inscripción)
    2. [Pago](#pago)
    3. [Reversa](#revertir-pago)
    4. [Desinscripción](#desinscripción)
- [Webpay Plus](#webpay-plus)
- [Test-Data](#test-data)
- [Producción](#producción)
## OneClick
La modalidad de pago Oneclick permite al tarjetahabiente realizar pagos en el comercio sin la
necesidad de ingresar cada vez información de la tarjeta de crédito al momento de realizar la
compra. El modelo de pago contempla un proceso previo de inscripción o enrolamiento del
tarjetahabiente, a través del comercio, que desee utilizar el servicio. Este tipo de pago facilita la
venta, disminuye el tiempo de la transacción y reduce los riesgos de ingreso erróneo de los datos
del medio de pago.

El proceso de integración con Webpay Oneclick consiste en desarrollar por parte del comercio las
llamadas a los servicios web dispuestos por Transbank para la inscripción de los tarjetahabientes,
así como para la realización de los pagos.

### Servicios

###### Inscripción
El método de inscripción requiere recibir por parámetros un email y un username.
Para realizar la inscrión basta con  hacer una llamada por GET a la siguiente dirección:

        /api/transbank/inscription?email=test@test.cl&username=test

Si se desea cambiar a POST, se puede modificar en /routes/api.php y los datos se deben enviar como JSON:

        {
        "email":"test@test.cl",
        "username":"test"
        }

**Importante:** Deben almacenarse en una base de datos el Tbk-Token y el username, necesarios para cobrar, revertir y eliminar la tarjeta.

###### Pago
 Para realizar un cobro, se necesita enviar por parámetros el monto a cobrar(amount), el username, una orden de compra(buyorder) y el tbk-token obtenido de la inscripción.
 La orden de compra debe generarse y debe ser unica de la compra.
 
 Ejemplo:
 
        /api/transbank/oneClickPayment?amount="MONTO"&username="USERNAME"&buyorder="ORDEN DE COMPRA"&tbkToken="TBK TOKEN"

Si se desea cambiar a POST, se puede modificar en /routes/api.php y los datos se deben enviar como JSON.

**Importante:** La orden de compra debe guardarse ya que es necesaria en caso de revertir la compra.
###### Revertir Pago
 Para realizar una reversa de un pago, se necesita enviar por parámetros la orden de compra.
        
        /api/transbank/reverse?buyorder="ORDERN DE COMPRA"

Si se desea cambiar a POST, se puede modificar en /routes/api.php y los datos se deben enviar como JSON.

###### Desinscripción
Para desinscribir una tarjeta, se necesita enviar por parámetros el username y el tbk token obtenido de la inscripción.

        /api/transbank/removeUser?username="USERNAME"&tbkToken="TBK TOKEN"

Si se desea cambiar a POST, se puede modificar en /routes/api.php y los datos se deben enviar como JSON.

## Webpay Plus
Webpay es una pasarela de pago desarrollada por Transbank para realizar transacciones desde Internet con tarjetas bancarías de crédito y débito. Hoy en día Webpay constituye una herramienta clave para el desarrollo de un comercio electrónico eficaz y seguro en Chile. 

En general un flujo de pago en Webpay se inicia desde el comercio, en donde el tarjetahabiente selecciona los productos o servicios a pagar. Una vez realizado esto, elige pagar con Webpay en donde, dependiendo de los productos contratados por el comercio, se despliegan las alternativas de pago de crédito con productos cuotas y débito Redcompra. Durante el proceso de pago se autentica el tarjetahabiente antes de realizar la transacción financiera, con el objetivo de validar que la tarjeta este siendo utilizada por el titular. Una vez resuelta la autenticación se procede a autorizar el pago. Webpay entrega al sistema del comercio el resultado de la autorización y si ésta es aprobada, Webpay emite un comprobante electrónico del pago.

### Servicios

###### initTransaction

Para realizar una transacción por webpay plus, se necesita enviar por parámetros el monto y la orden de compra.

        /api/transbank/webpay?amount=1000&buyorder=123456789

Si se desea cambiar a POST, se puede modificar en /routes/api.php y los datos se deben enviar como JSON.

## Test Data
Estos son los datos de tarjetas para que puedas probar en el ambiente de integración.  

![image](https://cloud.githubusercontent.com/assets/1103494/16890030/f125835c-4ab8-11e6-8bf9-847c847085a7.png)

#####VISA CREDIT CARD (SERÁ APROBADA)
Number: 4051885600446623
CVV: 123
Year: cualquiera
Month: cualquiera

#####MASTERCARD CREDIT CARD (SERÁ DENEGADA)
Number: 5186059559590568
CVV: 123
Year: cualquiera
Month: cualquiera

#####DEBIT CARD
CardNumber: 12345678

####BANK VIEW
RUT: 11.111.111-1
Password: 123

![captura de pantalla 2016-07-15 a las 6 28 41 p m](https://cloud.githubusercontent.com/assets/1103494/16890148/fdcf065e-4ab9-11e6-8d1a-83b9f8537c5c.png)

### Producción
El proyecto esta funcionando en modo de integración, para pasarlo a producción se deberá modificar el siguiente archivo:

    vendor/freshwork/transbank/src/CertificationBagFactory.php

Cambiando la asignacion de $certificationBag por la siguiente:    

    $certificationBag = new CertificationBag('/path/to/private.key', '/path/to/certificate.crt', null, CertificationBag::PRODUCTION);
