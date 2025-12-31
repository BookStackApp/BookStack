# BookStack to DokuWiki Migration Suite - Complete Guide

> **"The tragedy is not in the failing, but in the trying, and the trying again..."**  
> *— Every programmer at 3 AM trying to migrate data*

**Alex Alvonellos - i use arch btw**

---

## 🎭 The Tragedy We Face

You're here because you want to leave BookStack. Fair. It's a decent app, but maybe you want something lighter, faster, or just different. DokuWiki is a solid choice. 

The problem? Migration is hard. Data is messy. Frameworks break.

But we have tools. Multiple tools. In multiple languages. Because one language failing wasn't dramatic enough.

---

## 🚀 Quick Start (The Optimistic Path)

### For the Impatient

```bash
# The ultimate migration script
./ULTIMATE_MIGRATION.sh

# This does everything:
# ✓ Backs up your BookStack data
# ✓ Exports everything automatically
# ✓ Downloads and installs DokuWiki
# ✓ Imports your data
# ✓ Validates everything
# ✓ Generates copy-paste deployment instructions
```

### For the Pragmatic

```bash
# Just export your data using Perl (most reliable)
perl dev/migration/export-dokuwiki-perly.pl \
    -d bookstack \
    -u root \
    -P your_password \
    -o ./export

# Or use Java (slow but reliable)
java -jar dev/tools/bookstack2dokuwiki.jar \
    --db-name bookstack \
    --db-user root \
    --db-pass your_password \
    --output ./export

# Or use C (fastest option)
dev/tools/bookstack2dokuwiki \
    --db-host localhost \
    --db-name bookstack \
    --db-user root \
    --db-pass your_password \
    --output ./export
```

### For the Desperate

```bash
# When everything fails, get help from ChatGPT
perl diagnose-tragedy.pl
# This generates a diagnostic report
# Copy it to: https://chat.openai.com/
# Ask: "Help me fix this BookStack migration"
```

---

## 📚 Tools Available

We provide **FOUR** independent implementations because diversity is survival:

### 1. **PHP** (Laravel Command)
**Location:** `app/Console/Commands/ExportToDokuWiki.php`  
**Status:** ⚠️ Risky (but has automatic Perl fallback)  
**Speed:** Moderate  
**Reliability:** Low (will try Perl if it fails)

```bash
php artisan bookstack:export-dokuwiki --output-path=./export
```

### 2. **Perl** (Standalone Script) ✨ RECOMMENDED
**Location:** `dev/migration/export-dokuwiki-perly.pl`  
**Status:** ✅ Most Reliable  
**Speed:** Fast  
**Reliability:** High (blessed by Larry Wall himself)

```bash
perl dev/migration/export-dokuwiki-perly.pl \
    -d bookstack -u root -P password -o ./export \
    --validate-md5 -vv
```

Features:
- Direct database access (no framework overhead)
- MD5 validation of exported data
- Poetic error messages that bless your heart
- "Bless you" at every successful step

### 3. **Java** (Standalone JAR)
**Location:** `dev/tools/bookstack2dokuwiki.jar`  
**Status:** ✅ Reliable  
**Speed:** 🐌 Slow (prepare your coffee)  
**Reliability:** High

```bash
java -jar dev/tools/bookstack2dokuwiki.jar \
    --db-host localhost \
    --db-name bookstack \
    --db-user root \
    --db-pass password \
    --output ./export
```

Fun fact: While Java is starting up, Perl has already finished and gone home.

### 4. **C** (Native Binary)
**Location:** `dev/tools/bookstack2dokuwiki`  
**Status:** ✅ Fast & Reliable  
**Speed:** ⚡ Lightning  
**Reliability:** High

```bash
dev/tools/bookstack2dokuwiki \
    --db-host localhost \
    --db-name bookstack \
    --db-user root \
    --db-pass password \
    --output ./export
```

No framework, no interpretation, just raw speed.

### 5. **Shell (Emergency Only)**
**When:** Everything else fails  
**Speed:** Depends on luck  
**Reliability:** Last resort

```bash
./emergency-export.sh
```

---

## 🔄 Migration Process

### Step 1: Backup Everything

```bash
# Backup your database
mysqldump -h localhost -u root -p bookstack > backup.sql

# Backup uploads
cp -r storage/uploads storage/uploads.backup

# Create a full backup
zip -r bookstack-backup-$(date +%Y%m%d).zip . \
    -x "node_modules/*" "storage/uploads/*"
```

### Step 2: Export Data

Choose your tool from the ones above. Perl is recommended:

```bash
perl dev/migration/export-dokuwiki-perly.pl \
    -h localhost \
    -p 3306 \
    -d bookstack \
    -u root \
    -P your_password \
    -o ./dokuwiki-export \
    --validate-md5
```

### Step 3: Install DokuWiki

```bash
# Download DokuWiki
wget https://download.dokuwiki.org/src/dokuwiki/dokuwiki-stable.tgz

# Extract
tar -xzf dokuwiki-stable.tgz
mv dokuwiki-2024* dokuwiki

# Set permissions
chmod -R 755 dokuwiki
```

### Step 4: Import Data

```bash
# Copy exported data
cp -r dokuwiki-export/data/pages/* dokuwiki/data/pages/

# Fix permissions
chown -R www-data:www-data dokuwiki/data
chmod -R 775 dokuwiki/data/pages
```

### Step 5: Configure Web Server

**Apache:**
```apache
<VirtualHost *:80>
    ServerName wiki.example.com
    DocumentRoot /var/www/dokuwiki
    
    <Directory /var/www/dokuwiki>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name wiki.example.com;
    root /var/www/dokuwiki;
    index doku.php;
    
    location / {
        try_files $uri $uri/ @dokuwiki;
    }
    
    location @dokuwiki {
        rewrite ^/(.*) /doku.php?id=$1 last;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index doku.php;
        include fastcgi_params;
    }
}
```

### Step 6: Run DokuWiki Setup

```bash
# Visit: http://yoursite.com/install.php
# Complete the setup wizard
# Delete installer: rm dokuwiki/install.php
```

### Step 7: Rebuild Index

```bash
# Via web interface:
# Visit: http://yoursite.com/doku.php?do=index

# Or via CLI:
cd dokuwiki
sudo -u www-data php bin/indexer.php -c
```

---

## 🆘 When Everything Goes Wrong

### Run the Diagnostic

```bash
perl diagnose-tragedy.pl
```

This generates a comprehensive report showing:
- Your system configuration
- Available tools
- Database connectivity
- Recent errors
- A poetic assessment of your situation

### Send to ChatGPT

1. Run: `perl diagnose-tragedy.pl`
2. Go to: https://chat.openai.com/
3. Copy the entire DIAGNOSTIC_REPORT.txt
4. Ask: "Help me fix this BookStack migration"
5. Follow the exact commands it gives you

---

## 📋 Files in This Suite

### Main Scripts

| File | Purpose | Language |
|------|---------|----------|
| `ULTIMATE_MIGRATION.sh` | Complete migration in one script | Bash |
| `diagnose-tragedy.pl` | Gather diagnostics when things fail | Perl |
| `diagnose.sh` | Wrapper for diagnose-tragedy.pl | Bash |

### Export Tools

| Location | Tool | Language |
|----------|------|----------|
| `app/Console/Commands/ExportToDokuWiki.php` | Laravel command | PHP |
| `dev/migration/export-dokuwiki-perly.pl` | Standalone exporter | Perl |
| `dev/tools/bookstack2dokuwiki.jar` | Compiled JAR | Java |
| `dev/tools/bookstack2dokuwiki` | Native binary | C |
| `emergency-export.sh` | Last resort | Bash |

### Documentation

| File | Purpose |
|------|---------|
| `DOKUWIKI_MIGRATION.md` | Comprehensive migration guide |
| `MIGRATION_TOOLS.md` | Tool comparison and features |
| `COPY_PASTE_MIGRATION_GUIDE.md` | Exact commands to copy-paste |
| `COPY_PASTE_INSTRUCTIONS.txt` | Generated after migration |

### Tests

| File | Purpose |
|------|---------|
| `dev/tools/test-all.sh` | Test all implementations |
| `dev/tools/tests/test_perl.pl` | Perl tests |
| `dev/tools/tests/TestJava.java` | Java tests |
| `dev/tools/tests/test_c.sh` | C tests |
| `tests/Commands/ExportToDokuWikiTest.php` | PHP command tests |

---

## 🎓 Philosophy

This tool suite exists because:

1. **PHP Frameworks Fail** - Laravel has a tendency to break  
2. **One Option Isn't Enough** - We provide 4  
3. **Some Systems Need Different Tools** - Java, Perl, C, Shell  
4. **Failure Is Inevitable** - So we handle it gracefully  
5. **Documentation Matters** - And we documented everything  

> "The tragedy is not in the failing, but in the trying, and the trying again, 
> until we succeed or go mad trying."  
> — https://www.perlmonks.org/?node_id=1111395

---

## 🐧 Requirements

### Minimum

- Linux/Unix (Windows requires WSL)
- Bash
- MySQL client (`mysql` command)
- Perl 5.10+ (for best results)

### Optional But Recommended

- Perl modules: `DBI`, `DBD::mysql`
- Java (for JAR option)
- GCC and MySQL dev libraries (for C binary)
- PHP (for Laravel command option)

### Install Dependencies

**Ubuntu/Debian:**

```bash
# Perl and basic tools
sudo apt-get install perl libdbi-perl libdbd-mysql-perl mysql-client

# Java (optional)
sudo apt-get install default-jre

# Build tools (optional, for C compilation)
sudo apt-get install build-essential libmysqlclient-dev
```

**macOS (with Brew):**

```bash
# Perl modules
cpan install DBI DBD::mysql

# Java
brew install openjdk

# MySQL client
brew install mysql-client
```

---

## 🐱 Special Notes

### "Why is the code so funny?"

Because if we didn't laugh, we'd cry. Migration is tragic. We've embraced the tragedy with poetic error messages, ASCII art warnings, and philosophical commentary.

### "Why four languages?"

Because relying on one language is how you end up stuck:
- PHP fails → use Perl
- Perl not installed → use Java
- Java too slow → use C
- Everything else fails → use Shell

It's redundancy as reliability.

### "What's with all the 'Arch btw' jokes?"

Because this tool was created with love by ChatGPT for programmers who, let's face it, probably use Arch Linux (or think they should).

### "Should I use the PHP version?"

Only if you're feeling brave. Or sadistic. The PHP version has automatic Perl fallback, so if PHP fails (spoiler: it will), it automatically switches to Perl. It's like having a fire extinguisher built in.

---

## 🎊 Success!

If everything works:

1. ✅ Your data is safely backed up
2. ✅ Your data is exported to DokuWiki format
3. ✅ DokuWiki is installed and running
4. ✅ Your data is imported
5. ✅ Search index is rebuilt
6. ✅ You're free!

Congratulations! You've migrated from one PHP app to another PHP app!
(But at least DokuWiki is lighter.)

---

## 😱 If It Fails

1. Don't panic (panic is for amateurs)
2. Run: `perl diagnose-tragedy.pl`
3. Copy the report
4. Go to: https://chat.openai.com/
5. Paste the report
6. Ask for help
7. Follow the exact commands (copy-paste, no thinking required)
8. Success!

If ChatGPT can't help, at least you've documented your suffering beautifully.

---

## 🙏 Credits

**Developed with:**
- Coffee ☕
- Spite 😈
- Love ❤️
- Perl wisdom 📚
- A deep understanding of tragedy 🎭

**For:** Poor souls migrating from BookStack

**In the spirit of:** https://www.perlmonks.org/?node_id=1111395

---

## 📞 Getting Help

### Before You Ask

1. Run the diagnostic: `perl diagnose-tragedy.pl`
2. Check your .env file (do you have DB credentials?)
3. Verify MySQL is running: `systemctl status mysql`
4. Test DB connection: `mysql -uroot -p -D bookstack`

### When You Ask

**To ChatGPT:**
1. Go to: https://chat.openai.com/
2. Paste your diagnostic report
3. Ask: "Help me migrate from BookStack to DokuWiki"
4. Follow the exact commands given

**To GitHub:**
Create an issue with:
- Your diagnostic report
- What you've already tried
- The exact error message
- Your system information

### What NOT to Do

- Don't manually edit the PHP command (it works, trust it)
- Don't skip backups (seriously, backup first)
- Don't use PHP unless you're feeling lucky (use Perl)
- Don't give up (you can do this!)

---

## 🎬 Final Words

> "There is more than one way to do it." — Larry Wall

> "But one way is better than the others." — Us, right now

> "The tragedy is not in the failing..." — The PerlMonks

> "...but i use arch btw" — Everyone, always

Good luck. You've got this. And if you don't, ChatGPT does.

---

**Alex Alvonellos - i use arch btw**

*May your migrations be swift and your data be safe.*
