=== WooCommerce Fattureincloud Premium ===
Contributors: cristianozanca
Tags: fattureincloud, fatture, cloud, woocommerce, bill
Requires at least: 5.0
Tested up to: 6.1
Requires PHP: 7.4
Stable tag: 3.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


WooCommerce Fattureincloud Premium

== Description ==

The WooCommerce Fattureincloud plugin allows you to transform the orders received in your online store made with WooCommerce in Invoices on Fattureincloud.it

* The **Free Version** manages the last 10 orders and IVA at 0 and 22%
* The **Premium Version** manages all orders and IVA at 0, 22%, 23%, 24%, 4%, 5% and 10%

[More detailed info here](https://woofatture.com/documentazione/)

Try Fattureincloud for free at [this address](https://www.fattureincloud.it/service/form/form-registrazione/)

How does it work? = Select the order number from the drop-down menu, check in the preview that it is the right one and then send it to Fattureincloud.it

the WooCommerce plugin Fattureincloud requires the UID API and KEY API
that can be found at [this address](https://secure.fattureincloud.it/api)


== Changelog ==

= 3.0.1 =
* fix Iva 0% spedizione

= 3.0.0 =
* API 2.0

= 2.2.7 =
* Aggiunti i paesi EU alle aliquote Iva speciali 0% Vat, Se il cliente non ha sede in Italia il cap è 00000, il CF è vuoto, la Piva contiene il CF se inserito e se vuoto il programma inserisce in automatico il codice ISO del Paese estero e la parola ESTERO.

= 2.2.6 =
* fix checkout

= 2.2.5 =
* WC 6.4 compatibilità, nuova funione 19, CF field fix

= 2.2.4 =
* WC 6.3 compatibilità, corrispettivi upgrade

= 2.2.3 =
* WC 5.9 compatibilità, License check

= 2.2.2 =
* WC 5.5 compatibilità, alzato priorità hooks per estensione campi checkout, controllo attivazione licenza

= 2.2.1 =
* WC 5.4

= 2.2.0 =
* Cassa Previdenza bug fix

= 2.1.9 =
* WC 5.3 bug fix auto doc

= 2.1.8 =
* WC 5.2 and Cassa Previdenziale

= 2.1.7 =
* WC 5.1 and Rivalsa INPS

= 2.1.6 =
* fixes

= 2.1.5 =
* fixes nulla in checkout settings

= 2.1.4 =
* fixes, funzion 16, funzion 2, 3 e 4

= 2.1.3 =
* fixes check allow url

= 2.1.2 =
* WC 4.8

= 2.1.1 =
* enqueue js checkout page, shipping 0% vat fix, add check value CF VAT at checkout

= 2.1.0 =
* WC 4.6 , fixes placeholder for electronic billing

= 2.0.9 =
* WC 4.5 , fixes tax rate 22

= 2.0.8 =
* WC 4.4 , fixes variable products, fee

= 2.0.7 =
* WC 4.3 , fixes

= 2.0.6 =
* WC 4.2

= 2.0.5 =
* WC 4.1

= 2.0.4 =
* Fixes

= 2.0.3 =
* Custom Note, Shipping Tax

= 2.0.2 =
* WC 3.9

= 2.0.1 =
* WC 3.8 WP 5.3

= 2.0.0 =
* License

= 1.9.6 =
* WC 3.7 and fee

= 1.9.5 =
* fee

= 1.9.4 =
* upgrade library update

= 1.9.3 =
* new settings layout

= 1.9.2 =
* fix layout out of back-end

= 1.9.1 =
* vat international fix

= 1.9.0 =
* processing order, Iva multiple, Bollo2€, multiple customer choice

= 1.8.3 =
* fix i18n

= 1.8.2 =
* fix billing country

= 1.8.1 =
* sezionali automatici

= 1.8.0 =
* my account, sezionali, iva0% specifica, descrizione estesa

= 1.6.9 =
* fix spedizione

= 1.6.8 =
* cf se italiano, metodi pagamento FE multipli

= 1.6.7 =
* metodo pagamento

= 1.6.6 =
* Fattura Elettronica

= 1.6.5 =
* fix email, add registry data, new data values, short description in settings

= 1.6.4 =
* fix metod paymend message, default value

= 1.6.3 =
* fix css

= 1.6.2 =
* add SKU, Description, paid/unpaid invoice

= 1.6.1 =
* fixes

= 1.6.0 =
* automated email sent with invoice

= 1.5.4 =
* data api shown, improved shipping tax

= 1.5.3 =
* new upgrade system

= 1.5.2 =
* test update

= 1.5.1 =
* modalità pagamento, testo email, bottone creazione, compatibilità plugin esterno

= 1.5.0 =
* bill list & email send

= 1.0.2 =
* invio come ricevute

= 1.0.1 =
* msg error

= 1.0.0 =
* layout vari fix

= 0.5.2 =
* email - tabs added

= 0.5.0 =
* spedizione iva

= 0.4.2 =
* role shop manager

= 0.4.1 =
* fixes

= 0.4.0 =
* numero d'ordine

= 0.3.2 =
* descrizione e CF obbligatorio

= 0.3.0 =
* campi codice fiscale, partita iva

= 0.2.5 =
* segnalazione errori autosend e segnalazioni varie

= 0.2.4 =
* aggiunta funzione sperimentale invio automatico fatture

= 0.2.3 =
* prezzi senza iva inclusa

= 0.2.2 =
* Description

= 0.2.0 =
* Search order

= 0.1.0 =
* Initial release

== Upgrade Notice ==
* Initial release

== Installation ==

This section describes how to install the plugin and get it working.

1. Unzip `woo-fattureincloud-premium.zip`
2. Upload the `woo-fattureincloud-premium` directory (not its contents, the whole directory) to `/wp-content/plugins/`
3. Activate the plugin through the `Plugins` menu in WordPress

== Frequently Asked Questions ==

= I prezzi li posso mettere sia con iva inclusa ed iva esclusa? =

No è necessario impostare l'opzione "No, inserirò prezzi al netto di imposta"
Nel negozio online i prezzi possono essere comunque mostrati iva inclusa

== Screenshots ==

1. Invio Riuscito!
2. Quando l'invio non riesce appare la motivazione
3. Ecco le fatture inviate

== Credits ==
* Huge thanks to LoicTheAztec, Pascal Knecht, Rodolfo Melogli, Roberto Kalamun Pasini, Rynaldo