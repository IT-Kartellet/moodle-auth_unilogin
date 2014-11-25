# Moodle UNI•Login authentication
(english version below - at some point ...)

Dette plugin lader dine brugere logge ind i moodle ved brug af deres UNI•Login. 

## Setup
* Pluginet placeres i din moodle folder under auth/unilogin
* Du skal bruge en applikationskode samt secret fra UNI•C   

Når du har placeret pluginet i den rigtige mappe skal du logge ind som administrator og installere pluginet. Hvis du ikke automatisk bliver viderestillet til installationen skal du gå ind under "Site administration" og vælge "Notifications". Efter installationen bliver du bedt om at tage stilling til nogle indstillinger:

## Indstillinger
### Applikations ID samt secret
Dette får du oplyst af UNI•C  ved oprettelse.

### Login type
Det er muligt at logge brugere ind på to måder, med Single Sign-On (SSO) eller Single Log In (SLI). Ved SSO bliver brugere automatisk logget ind hvis de allerede er logget på en anden service med deres UNI•Login, og skal altså ikke indtaste deres login igen. Ved SLI skal brugere altid indtaste deres login.

### Valideringsstrategi
Når en bruger har logget ind med sit UNI•Login sendes han tilbage til moodle med en billet. Udover tjekket for at billeten matcher den secret der er aftalt med UNI•C, som altid udføres, kan billetten valideres på to måder, for at sikre at den ikke kommer fra en ondsindet person som har opsnappet en anden brugers billet. 
Billetten kan enten valideres ved at gemme alle modtagne billetter i databasen for at sikre at en billet kun bruges en gang, eller ved at tjekke at billetten er udstedt inden for et vist tidsrum (se nedenfor).

### Valideringsvindue
Den maksimale alder, i sekunder, en billet må have for at blive betragtet som valid. Vinduet må ikke være kortere end den tid det tager at viderestille brugerens browser. 
Denne indstilling har kun effekt hvis valideringsstrategien er sat til "Tid" ovenfor.