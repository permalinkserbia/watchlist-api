# Watchlist API

REST API za upravljanje ličnom listom filmova. Korisnici mogu da se registruju, prijave i dodaju filmove na watchlist koristeći IMDb ID. Podaci o filmovima se automatski preuzimaju sa [OMDB API](https://www.omdbapi.com/) servisa.

## Tehnologije

| Tehnologija | Verzija | Namena |
|---|---|---|
| PHP | ^8.3 | Backend jezik |
| Laravel | ^13.8 | Web framework |
| Laravel Sanctum | ^4.0 | API autentifikacija (Bearer token) |
| MySQL / SQLite | — | Relaciona baza podataka |
| OMDB API | — | Spoljni servis za podatke o filmovima |

### Dev alati

- **Laravel Pint** — formatiranje PHP koda
- **PHPUnit** — automatsko testiranje (`php artisan test`)
- **Postman** — ručno testiranje API-ja preko kolekcije
- **Laravel Pail** — logovanje u development okruženju

## Testiranje

### PHPUnit

```bash
cd watchlist-api
php artisan test
```

### Postman kolekcija

Za ručno testiranje API-ja u Postman-u, u projektu je dostupna gotova kolekcija:

```
watchlist-api/Watchlist API.postman_collection.json
```

**Kako koristiti:**

1. Otvori Postman
2. Klikni **Import** i uvezi fajl `Watchlist API.postman_collection.json`
3. Pokreni Laravel server: `php artisan serve`
4. Prvo pošalji **Register** ili **Login** zahtev da dobiješ Bearer token
5. Token postavi u Authorization → Bearer Token za zaštićene rute

Kolekcija koristi `http://127.0.0.1:8000` kao bazni URL.

## Arhitektura

Projekat prati slojevitu arhitekturu:

```
Controller → Service → Repository → Model
```

| Sloj | Odgovornost |
|---|---|
| **Controller** | Validacija zahteva, poziv servisa, JSON odgovor |
| **Service** | Poslovna logika (`OmdbMovieService`, `WatchlistService`) |
| **Repository** | Kompleksni upiti ka bazi (`MovieRepository`, `WatchlistItemRepository`) |
| **Provider** | Integracija sa OMDB API-jem (`OmdbMovieProvider`) |

## Instalacija

```bash
cd watchlist-api

composer install

cp .env.example .env
php artisan key:generate

# Podesi bazu u .env fajlu, zatim:
php artisan migrate

php artisan serve
```

API je dostupan na `http://127.0.0.1:8000/api`.

## Konfiguracija (.env)

Za testiranje možeš koristiti sledeći OMDB API ključ:

```env
OMDB_API_KEY=3cbad630
```

Primer minimalne `.env` konfiguracije:

```env
APP_NAME=WatchlistAPI
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=watchlist_api
DB_USERNAME=root
DB_PASSWORD=

OMDB_API_KEY=3cbad630
```

## Autentifikacija

API koristi Laravel Sanctum sa Bearer tokenom. Neautentifikovani zahtevi na zaštićene rute vraćaju `401` u JSON formatu.

```
Authorization: Bearer {token}
```

Token se dobija pri registraciji ili prijavi.

---

## API rute

### Autentifikacija

#### Registracija

```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Odgovor (201):**

```json
{
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
  "token": "1|..."
}
```

#### Prijava

```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Odjava

```http
POST /api/logout
Authorization: Bearer {token}
```

#### Trenutni korisnik

```http
GET /api/user
Authorization: Bearer {token}
```

---

### Watchlist

#### Lista filmova

```http
GET /api/watchlist
Authorization: Bearer {token}

# Opciono filtriranje po statusu:
GET /api/watchlist?status=pending
```

Mogući statusi: `pending`, `watched`, `skipped`.

#### Dodavanje filma

Film se preuzima sa OMDB servisa po IMDb ID-u i čuva u bazi. `rating` se automatski popunjava iz OMDB odgovora.

```http
POST /api/watchlist
Authorization: Bearer {token}
Content-Type: application/json

{
  "imdb_id": "tt0111161",
  "status": "pending",
  "notes": "Must watch"
}
```

**Primer IMDb ID-eva za testiranje:**

| Film | IMDb ID |
|---|---|
| The Shawshank Redemption | `tt0111161` |
| The Godfather | `tt0068646` |
| The Dark Knight | `tt0468569` |

#### Ažuriranje stavke

```http
PUT /api/watchlist/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "watched",
  "notes": "Odličan film"
}
```

#### Uklanjanje sa watchlist-e

```http
DELETE /api/watchlist/{id}
Authorization: Bearer {token}
```

**Odgovor:**

```json
{
  "message": "Movie removed from watchlist"
}
```

---

## Modeli

### Movie

Podaci preuzeti sa OMDB servisa: `title`, `external_id`, `year`, `genre`, `poster`, `plot`, `runtime`, `imb_rating`, `status`.

### WatchlistItem

Korisnička stavka na listi: `movie_id`, `user_id`, `status`, `rating` (iz OMDB-a), `notes`.

## Licenca

MIT
