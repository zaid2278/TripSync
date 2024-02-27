<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trip Sync</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=PT+Sans">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css">
    <script src="https://kit.fontawesome.com/bbb7b3d47b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body id="page-top">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="#page-top">Trip Sync <i class="fa-solid fa-ship"></i></a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#services">Mapping <i class="fa-solid fa-map-location"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="masthead">
        <div class="container px-4 px-lg-5 h-100">
            <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-8 align-self-end">
                    <h1 class="text-white font-weight-bold">Discover Oceans With Trip Sync</h1>
                    <hr class="divider">
                </div>
                <div class="col-lg-8 align-self-baseline">
                    <p class="text-white-75 mb-5">Building Maps for Oceans</p>
                    <a class="btn btn-primary btn-xl" href="#services">Let's Begin</a>
                </div>
            </div>
        </div>
    </header>

    <section class="page-section" id="services">
        <div class="container px-4 px-lg-5">
            <h2 class="text-center mt-0">Let's Begin The Search!</h2>
            <hr class="divider" />
            <br>

            <div class="seccontainer">
                <div class="seccontainer__item">
                    <form method="post" action="inputHandle.php">
                        <input type="text" name="location" class="secform__field" id="searchTbx" placeholder="Enter A Location" />
                        <button type="button" class="secbtn secbtn--primary secbtn--inside uppercase" onclick="Search()">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <div class="maps">
        <script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?callback=GetMap' async defer></script>
        <div id="myMap" style="width: 1700px; height: 800px; margin: 0 auto;"></div>
    </div>
    <br><br>

    <?php
    $njit_dsn = 'mysql:host=sql1.njit.edu;port=3306;dbname=mnm23';
    $njit_username = 'mnm23';
    $njit_password = 'SQLdatabase1*';

    try {
        $database = new PDO($njit_dsn, $njit_username, $njit_password);
    } 
    catch (PDOException $exception) {
        $error_message = $exception->getMessage();
        include('databaseError.php');
        exit();
    }

    $query = 'SELECT Latitude, Longitude FROM mytable';
    $statement = $database->prepare($query);
    $statement->execute();
    $portsData = $statement->fetchAll();
    $statement->closeCursor();

    $queryShip  = 'SELECT Latitude, Longitude FROM mytableShipOne';
    $statementTwo = $database->prepare($queryShip);
    $statementTwo->execute();
    $shipdataOne = $statementTwo->fetchAll();
    $statementTwo->closeCursor();

    $queryShipTwo  = 'SELECT Latitude, Longitude FROM mytableShipTwo';
    $statementThree = $database->prepare($queryShipTwo);
    $statementThree->execute();
    $shipdataTwo = $statementThree->fetchAll();
    $statementThree->closeCursor();
    ?>

    <script>
        var map, searchManager;
        function GetMap() {
            map = new Microsoft.Maps.Map('#myMap', {
                credentials: 'AtTAdslVkIpX4aQPLrDCOw6tjV2AvulBk6u1G3oaWlRc6BykGyb_ymvImFosPxAm',
                center: new Microsoft.Maps.Location(40.057347, -74.414532)
                
            });
            <?php
                foreach ($portsData as $data) {
                    echo "var location = new Microsoft.Maps.Location(" . $data['Latitude'] . ", " . $data['Longitude'] . ");\n";
                    echo "var pin = new Microsoft.Maps.Pushpin(location, { title: 'Port', text: 'P' });\n";
                    echo "map.entities.push(pin);\n";
                }
            ?>
            <?php
                foreach ($shipdataOne as $shipdataOne) {
                    echo "var location = new Microsoft.Maps.Location(" . $shipdataOne['Latitude'] . ", " . $shipdataOne['Longitude'] . ");\n";
                    echo "var pin = new Microsoft.Maps.Pushpin(location, { title: 'Ship', text: 'S' });\n";
                    echo "map.entities.push(pin);\n";
                }
            ?>
            <?php
                foreach ($shipdataTwo as $shipdataTwo) {
                    echo "var location = new Microsoft.Maps.Location(" . $shipdataTwo['Latitude'] . ", " . $shipdataTwo['Longitude'] . ");\n";
                    echo "var pin = new Microsoft.Maps.Pushpin(location, { title: 'Ship', text: 'S' });\n";
                    echo "map.entities.push(pin);\n";
                }
            ?>
        }

        function Search() {
            if (!searchManager) {
                Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
                    searchManager = new Microsoft.Maps.Search.SearchManager(map);
                    geocodeQuery();
                });
            } else {
                geocodeQuery();
            }
        }

        function geocodeQuery() {
            var query = document.getElementById('searchTbx').value;
            var searchRequest = {
                where: query,
                callback: function (r) {
                    if (r && r.results && r.results.length > 0) {
                        var loc = r.results[0].location;
                        map.setView({ center: loc, zoom: 10 });
                    } else {
                        alert("No results found.");
                    }
                },
                errorCallback: function (e) {
                    alert("An error occurred while searching for the location.");
                }
            };
            searchManager.geocode(searchRequest);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
</body>
</html>