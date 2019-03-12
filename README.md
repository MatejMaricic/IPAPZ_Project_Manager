# IPAPZ_Project_Manager

Nužne funkcionalnosti:
1. Dvije vrste usera: project manager i developer
2. Project manager pravi strukturu projekta, definira tim koji će raditi na projektu
3. project manager dodaje zadatke članovima tima
4. Project mamanger može na projektu odrediti statuse koje želi koristiti(za tracking projekta)
5. Komunikacija između tima

Poželjne funkcionalnosti:
1.Svi useri imaju profilnu sliku
2.Zadatak može imati jednu ili više slika
3. zadatak ima status(progress),status je vezan za project managera
4. paginacija taskova



Opcionalne
3.TinyMCE 5 

Entity

project manager
developer
project
project status
task
task status
comments





Project Manager može kreirat više projekata (one to many)
projekt može imati više developera a developer može raditi na više projekata(many to many)
projekt može imati više zadataka(one to many)
projekt može imat više statusa (one to many)
developer može raditi više zadataka i zadatak može imati više developera (many to many)
zadatak može imati više statusa (one to many)



Zadatak:

1. Popraviti sve što trenutno ne radi.
2. Dodati subscribere za pojedini task koji primaju email notifikacije na svakom postu(cron).
Subscriberi su svi koji su assignani na task i svi ostali koji prate taj task ( subscribe button).
3. Taskovi se ne brišu nego completaju, ukoliko je completan prebacuje se u listu completanih taskova
te se može ponovo otvorit na lisu nedovršenih taskova.
4. Upisani sati mogu biti bilabilni i nebilabilni. Mogućnost filtracije na listi sati.
5. Dodati rasprave(discussione) koji se mogu konvertirati u task pritiskom na button.
Discussioni se mogu obrisati trajno i nemaju propretije kao taskovi, ali imaju listu subscribera.
Na discussionu mora pisati tko ga je otvorio i kada.
6. Dolaskom na projekt dolazi se na page gdje su ponuđeni taskovi i discussioni i klikom se ulazi
u njihovo izlistanje.
7. Projekti se mogu kao i taskovi completeati od strane admina ili potpuno obrisati.
8. Dodati filtriranje po osobama na listi taskova (prikazuju se taskovi samo za izabranog usera).
9. Upisivanje sati na task s opisom rada od strane usera. Mogucnost dodavanje estimatea za svaki task.
Ispisati koliko je vremena potrošeno na task.
10. Dodati mogućnost ispisivanja upisanih sati za sve zaposlenike(pojedinačno i zajedno)
sa biranjem vremenskog okvira i specificiranjem projekta/za sve projekte.
11. Mogućnost exporta tjedne/mjesečne potrošnje sati na određenom projektu.
12. Poželjno je promisliti o implementaciji te modificirati dodatne stvari kako bi korisniku
približili aplikaciju. Slobodno dodati nove funkcionalnosti. Postojeće funkcionalnosti ukratko dokumentirati u Features/ {određeni entity}.md k
kako bi aplikacija bila preglednija i lakša za upoznavanje.

