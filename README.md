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


