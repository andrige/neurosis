<<<<<<< HEAD
<h2>Kmom07: Färdigställ och produktifiera MVC-ramverket</h2>
<p><strong>Vilken uppfattning har du om Git och GitHub?</strong><br/>
Det är ett fantastiskt bra verktyg och helt gratis! Så länge man kan relevanta kommandon så är det inte mycket som hindrar en från att enkelt lägga upp hela sitt projekt på nätet, vilket är rätt häftigt. Det är väldigt bra för open-source projekt och att enkelt kunna bidra till något större tillsammans. Det som man kan ifrågasätta är hur pass nödvändigt det är för mig att lägga upp något på GitHub när jag inte har för avsikt (än så länge) att göra ett öppet projekt, men det är behändigt att clona andras ramverk för mina egna projekt!
  </p>
<p><strong>Gick det smärtfritt att publicera ditt ramverk på GitHub?</strong><br/>
Relativt, hade väl uppskattat en cheat-sheet på en plats som summerade alla relevanta kommandon istället för utspridda över ett flertal sidor och moment. Vet fortfarande inte riktigt hur jag ska lägga på 'tags' för att ha versionskontrollering under samma repository, men det får jag lära mig på egen hand senare.
  </p>
<p><strong>Skriv en kort summering av ditt ramverk, är det plain Lydia eller har det några extra features?</strong><br/>
Det är Lydia under annat namn. Det svider att följa så noga exempelkoden, men denna typ av projekt måste ha varit kanske tre gånger större än det vi gjort innan, med mycket mer komplexitet och dessutom med flera open-source ramverk i ryggmärgen. Jag lade dock till en ändring i CCTheme, som kändes behövlig. Sidan gjorde nämligen ingenting vad det gäller att kompilera '<code>style.less</code>' filen såvida jag inte gick in i '<code>config.php</code>' och ändrade path och stylesheet filnamnet. Så eftersom <code>CCTheme::__construct()</code> laddas in efter config.php och före <code>CViewContainer</code> kunde jag helt enkelt bara uppdatera den pathen och stylesheet-namnet i endast theme-sidan. Så när jag är i theme kan jag kompilera '<code>style.less</code>', men inte annanstans. Får se till att skydda sidan med admin-nivå senare, men det räcker för tillfället när sidan är under utveckling.
  </p>
<p><strong>Beskriv din testinstallation, gick den smidigt att göra eller var det något som krånglade, borde ramverket varit annorlunda?</strong><br/>
Jo jag upptäckte att <code>CMContent::Manage()</code> försöker ta tag i den inloggade användarens namn för att skapa nya inlägg i dess namn, men eftersom sidan installeras i den stunden så finns det faktiskt ingen inloggad så inläggen saknar sin row 'idUser'. Löste det genom att lägga in <code>$this->Login('root','root')</code> i <code>CMContent::Manage()</code> innan inläggen skapas i databasen. Det finns helt klart mycket man kan göra när det gäller sidans struktur och presentation. Bland annat så hade jag gärna sett så att sidan leder en endast till modulinstallationen om den inte varit gjort, eller att man skulle kunna anpassa top-navigations menyn i webbläsaren. I alla fall, det är såna ändringar som jag tror jag kan tackla, men behöver helt klart gå över till sista kursmomentet före jag kan lyxa till med såna extra funktioner.
  </p>
<p><strong>Hur installerar man ramverket?</strong><br/>
Man drar ner det från mitt repository via kommandot: <code>git clone git://github.com/andrige/neurosis.git</code>, sätter sedan behörigheten till mappen '<code>site/data</code>' till <code>chmod 777</code> för att tillåta att sidan skapar databsen. Därefter kan man via index (tryck på logotypen) finna i en lista av länkar '<code>module/install</code>'. Klicka på den och du ska ha skapat databas med användare, samt även logga in dig som användare <code>root</code> med lösenordet <code>root</code>.
  </p>
  Projektet kan du hitta på <a href="https://github.com/andrige/neurosis">GitHub</a>
=======
<h2>Kmom02: Grunden till ett MVC-ramverk </h2>
  <p><strong>Vilken uppfattning har du om Git och GitHub?</strong><br/>
    </p>
  <p><strong>Gick det smärtfritt att publicera ditt ramverk på GitHub?</strong><br/>
    </p>
  <p><strong>Skriv en kort summering av ditt ramverk, är det plain Lydia eller har det några extra features?</strong><br/>
    </p>
  <p><strong>Beskriv din testinstallation, gick den smidigt att göra eller var det något som krånglade, borde ramverket varit annorlunda?</strong><br/>
    </p>
    Projektet kan du hitta på <a href="https://github.com/andrige/neurosis">GitHub</a>
>>>>>>> 62c6a280ead8529bd9558ffe31a0e42cded6ca2f
