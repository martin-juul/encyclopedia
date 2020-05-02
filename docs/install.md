# Installation

## System requirements

+ Linux or macOS
+ Docker Desktop or docker-ce
+ docker-compose

### Hardware Recommendations

These are recommendations, but not necessarily required.
Do note that the database will be under heavy load during import and search indexing.
While redis and meilisearch will be pegged during search indexing.

Postgres: 4GB Ram, 2 vcpu, 100GB SSD for full dumps or 20GB SSD/Flash for partial.

Redis: 2GB Ram and 0.5 vcpu

Meilisearch: 512MB ram and 1 vcpu

Web app: 512MB ram and 1 vcpu

Worker: 512MB ram and 2 vcpu

On a Macbook Pro Mid-2015, 2.8Ghz 4c/8t, 16GB ram and NVME.
The importer has a throughput of 1GB/10 Minutes. Turbo boost should be disabled (on notebooks), as the frequent up/down clocking
has a severe impact on performance. Coupled with the atrocious schedulers on desktop systems (non-issue on ubuntu server, debian and fedora server).

A full wikipedia article dump expands to ~80GB, before parsing. As of May 2020.

#### Raspberry PI and other SBCs

Do not import the full article dump. It'll crash and burn.

Instead, you should only attempt to import the smaller dumps Wikipedia provides.

## Setup

Copy [docker-compose.yml](/docker-compose.yml) to a directory (i.e. /srv/encyclopedia)

Run `docker-compose up -d` from the directory.

Verify the app is running on `http://localhost:9505`

Continue on to [CLI: Create admin user](/docs/cli-create-admin-user.md)
