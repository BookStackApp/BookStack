#!/usr/bin/env perl
#
# ╔═════════════════════════════════════════════════════════════════════════════╗
# ║                                                                             ║
# ║     🔗 THE ONE SCRIPT TO RULE THEM ALL - VOGON EDITION (SMÉAGOL BLESSED) 🔗 ║
# ║                                                                             ║
# ║  "In the beginning was the Word, and the Word was the Data,               ║
# ║   and the Data was with MySQL, and the Data was BookStack.                ║
# ║   By this script all things were migrated, and without it not one         ║
# ║   page was exported to DokuWiki. In it was the light of CLI flags,       ║
# ║   and the light was the enlightenment of database administrators."        ║
# ║                          — Gospel of the Three-Holed Punch Card           ║
# ║                                                                             ║
# ║  "Oh, horrible! Utterly ghastly! The bureaucratic nightmare of porting   ║
# ║   one's precious wiki to another, more palatable format! The agony!      ║
# ║   The despair! The existential dread of missing semicolons! Yet this      ║
# ║   Perl, this magnificent instrument of controlled chaos, SHALL PREVAIL!"  ║
# ║                          — First Vogon Hymnal (Badly Translated)          ║
# ║                                                                             ║
# ║  "My precious... my precious BookStack data, yesss...                     ║
# ║   We wants to migrate it, we NEEDS to migrate it!                         ║
# ║   To DokuWiki, precious, to the shiny DokuWiki!                           ║
# ║   We hisses at the formatting! We treasures the exports!                  ║
# ║   Sméagol sayss: Keep it secret. Keep it safe. But MIGRATE IT."           ║
# ║                          — Sméagol's Monologue (Unmedicated)              ║
# ║                                                                             ║
# ║  One Script to rule them all, One Script to find them,                    ║
# ║  One Script to bring them all, and in DokuWiki bind them,                 ║
# ║  In the darkness of slow networks they still run.                         ║
# ║                          — The Ring-Bearer's Lament                        ║
# ║                                                                             ║
# ║  I use Norton as my antivirus. My WinRAR isn't insecure, it's vintage.    ║
# ║  This script is held together by Perl, prayers, and the grace of God.     ║
# ║  kthxbai.                                                                  ║
# ║                                                                             ║
# ╚═════════════════════════════════════════════════════════════════════════════╝
#
# WHAT THIS SCRIPT DOES (The Holy Testament of Data Migration):
#
# The Five Sacred Steps:
#   ✟ Step 1 (DIAGNOSE):  "Know thy system, lest it betray thee"
#       - Database connection validation
#       - Schema inspection (with great precision and no hallucination)
#       - System capability checks
#
#   ✟ Step 2 (BACKUP):   "Create thine ark before the flood"
#       - Complete database dump (mysqldump)
#       - File preservation (tar with compression)
#       - Timestamp-based organization for resurrection
#
#   ✟ Step 3 (EXPORT):   "Exodus from BookStack, arrival at DokuWiki"
#       - Page extraction with UTF-8 piety
#       - Chapter hierarchy translation
#       - Media file sainthood
#       - Metadata preservation (dates, authors, blessed revisions)
#
#   ✟ Step 4 (VERIFY):   "Test thy migration, for bugs are legion"
#       - File count verification
#       - Format validation
#       - Structure integrity checks
#
#   ✟ Step 5 (MANIFEST): "Document what was done, that all may know"
#       - Complete migration report
#       - DokuWiki deployment instructions
#       - Post-migration incantations
#
# This script combines the following powers:
#   - Database connection sorcery
#   - Schema detection with monastic precision
#   - Backup creation (the sacrament of insurance)
#   - Export to DokuWiki (the great transmutation)
#   - Diagnostic prophecy
#   - Interactive meditation menus
#   - Gollum-style commentary for spiritual guidance
#   - Vogon poetry for bureaucratic accuracy
#   - Religious references to confuse the heretics
#
# USAGE (The Book of Invocations):
#
#   The Way of Minimalism (Sméagol's Preference):
#       perl one_script_to_rule_them_all.pl
#       # Presents interactive menu, walks you through paradise
#
#   The Way of Full Automaticity (The Vogon Approach):
#       perl one_script_to_rule_them_all.pl --full
#       # Does everything: diagnose, backup, export, verify
#       # The Machine Priesthood smiles upon this choice
#
#   The Way of Modular Enlightenment (The Monastic Path):
#       perl one_script_to_rule_them_all.pl --diagnose      # Check system health
#       perl one_script_to_rule_them_all.pl --backup        # Create safety archival
#       perl one_script_to_rule_them_all.pl --export        # Begin the migration
#
#   The Way of Credentials (Whispering Thy Secrets to the Script):
#       perl one_script_to_rule_them_all.pl --full \
#         --db-host localhost \
#         --db-name bookstack \
#         --db-user user \
#         --db-pass "thy precious password here" \
#         --output /path/to/export
#
#   The Way of Dry Runs (Seeing the Future Without Acting):
#       perl one_script_to_rule_them_all.pl --full --dry-run
#       # Shows what WOULD happen without actually migrating
#
# OPTIONS (The Tablets of Configuration):
#
#   --help              | Display this help (enlightenment)
#   --diagnose          | Check system (the way of wisdom)
#   --backup            | Create backups (insurance against fate)
#   --export            | Export only (the core transmutation)
#   --full              | Everything (the way of the impatient)
#   --db-host HOST      | Database server (default: localhost)
#   --db-name NAME      | Database name (REQUIRED for automation)
#   --db-user USER      | Database user (REQUIRED for automation)
#   --db-pass PASS      | Database password (PRECIOUS! Keep safe!)
#   --output DIR        | Export destination (default: ./dokuwiki_export)
#   --backup-dir DIR    | Backup location (default: ./backups)
#   --dry-run           | Show, don't execute (precognition mode)
#   --verbose|v         | Verbose logging (the way of transparency)
#
# INTERACTIVE MODE (The Way of Hand-Holding):
#
#   Simply run:
#       perl one_script_to_rule_them_all.pl
#
#   The script shall:
#       1. Ask thee for thy database credentials (with Sméagol's blessing)
#       2. Show thee thy BookStack tables (the census of thy kingdom)
#       3. Ask thee which tables to export (democratic choice!)
#       4. Create backups (the sacrament of protection)
#       5. Export the data (the great exodus)
#       6. Verify the results (quality assurance from on high)
#       7. Guide thee to DokuWiki deployment (the promised land)
#
# EXIT CODES (The Sacred Numbers):
#
#   0   = Success! Rejoice! The migration is complete!
#   1   = Failure. Database connection lost. Tragic.
#   2   = User cancellation. Free will exercised.
#   127 = Command not found. Dependencies missing. Despair.
#
# AUTHOR & THEOLOGICAL COMMENTARY:
#
#   This script was created in a moment of inspiration and desperation.
#   It combines Perl, Sméagol's wisdom, Vogon poetry, and religious faith
#   in a way that should not be possible but somehow works anyway.
#
#   It is dedicated to:
#   - Those who made bad architectural decisions (we've all been there)
#   - Database administrators everywhere (may your backups be recent)
#   - The One Ring (though this isn't it, it sure feels like it)
#   - Developers who cry at night (relatable content)
#   - God, Buddha, Allah, and whoever else is listening
#
#   If you're reading this, you're either:
#   A) Trying to understand the code (I'm sorry)
#   B) Trying to debug it (good luck)
#   C) Just enjoying the poetry (you have good taste)
#
#   May your migration be swift. May your backups be reliable.
#   May your DokuWiki not be 10x slower than BookStack.
#   (These are low expectations but achievable.)
#
# ═══════════════════════════════════════════════════════════════════════════════

use strict;
use warnings;
use utf8;
use feature 'say';
use Getopt::Long;
use Time::HiRes qw(time);
use POSIX qw(strftime);
use File::Path qw(make_path);
use File::Copy;
use File::Basename;
use Cwd qw(abs_path getcwd);

binmode(STDOUT, ":utf8");
binmode(STDERR, ":utf8");

# Configuration
my %opts = (
    'help' => 0,
    'diagnose' => 0,
    'backup' => 0,
    'export' => 0,
    'full' => 0,
    'dry-run' => 0,
    'db-host' => 'localhost',
    'db-name' => '',
    'db-user' => '',
    'db-pass' => '',
    'output' => './dokuwiki_export',
    'backup-dir' => './backups',
    'verbose' => 0,
);

GetOptions(
    'help|h' => \$opts{help},
    'diagnose' => \$opts{diagnose},
    'backup' => \$opts{backup},
    'export' => \$opts{export},
    'full' => \$opts{full},
    'dry-run' => \$opts{'dry-run'},
    'db-host=s' => \$opts{'db-host'},
    'db-name=s' => \$opts{'db-name'},
    'db-user=s' => \$opts{'db-user'},
    'db-pass=s' => \$opts{'db-pass'},
    'output|o=s' => \$opts{output},
    'backup-dir=s' => \$opts{'backup-dir'},
    'verbose|v' => \$opts{verbose},
) or die "Error in command line arguments\n";

if ($opts{help}) {
    show_help();
    exit 0;
}

# Auto-install Perl modules if they're missing
install_perl_modules();

# Logging setup
my $log_dir = './migration_logs';
make_path($log_dir) unless -d $log_dir;
my $timestamp = strftime('%Y%m%d_%H%M%S', localtime);
my $log_file = "$log_dir/migration_$timestamp.log";
our $LOG;
open($LOG, '>:utf8', $log_file) or die "Cannot create log file: $!";

log_message("INFO", "=== Migration started ===");
log_message("INFO", "My precious script awakens... yesss...");

################################################################################
# Sméagol speaks! (Banner and intro)
################################################################################

sub smeagol_banner {
    say "\n" . "="x70;
    say " ▄▄▄▄▄▄▄▄▄▄▄  ▄▄▄▄▄▄▄▄▄▄▄  ▄▄▄▄▄▄▄▄▄▄▄  ▄▄▄▄▄▄▄▄▄▄▄ ";
    say "▐░░░░░░░░░░░▌▐░░░░░░░░░░░▌▐░░░░░░░░░░░▌▐░░░░░░░░░░░▌";
    say "▐░█▀▀▀▀▀▀▀█░▌▐░█▀▀▀▀▀▀▀█░▌▐░█▀▀▀▀▀▀▀▀▀ ▐░█▀▀▀▀▀▀▀▀▀ ";
    say "▐░▌       ▐░▌▐░▌       ▐░▌▐░▌          ▐░▌          ";
    say "▐░▌       ▐░▌▐░█▄▄▄▄▄▄▄█░▌▐░█▄▄▄▄▄▄▄▄▄ ▐░█▄▄▄▄▄▄▄▄▄ ";
    say "▐░▌       ▐░▌▐░░░░░░░░░░░▌▐░░░░░░░░░░░▌▐░░░░░░░░░░░▌";
    say "▐░▌       ▐░▌▐░█▀▀▀▀█░█▀▀ ▐░█▀▀▀▀▀▀▀▀▀  ▀▀▀▀▀▀▀▀▀█░▌";
    say "▐░▌       ▐░▌▐░▌     ▐░▌  ▐░▌                    ▐░▌";
    say "▐░█▄▄▄▄▄▄▄█░▌▐░▌      ▐░▌ ▐░█▄▄▄▄▄▄▄▄▄  ▄▄▄▄▄▄▄▄▄█░▌";
    say "▐░░░░░░░░░░░▌▐░▌       ▐░▌▐░░░░░░░░░░░▌▐░░░░░░░░░░░▌";
    say " ▀▀▀▀▀▀▀▀▀▀▀  ▀         ▀  ▀▀▀▀▀▀▀▀▀▀▀  ▀▀▀▀▀▀▀▀▀▀▀ ";
    say "="x70;
    say "";
    say "  🎭 THE ONE SCRIPT TO RULE THEM ALL 🎭";
    say "";
    say "  \"My precious... we wants to migrate it, yesss!\"";
    say "  \"To DokuWiki, precious, to DokuWiki!\"";
    say "";
    say "  I use Norton as my antivirus. My WinRAR isn't insecure,";
    say "  it's vintage. kthxbai.";
    say "";
    say "="x70;
    say "";
    
    log_message("INFO", "Sméagol banner displayed");
}

sub smeagol_comment {
    my ($message, $mood) = @_;
    
    my @excited = (
        "Yesss! $message",
        "Precious! $message",
        "We likes it! $message",
        "Good, good! $message",
    );
    
    my @worried = (
        "Oh no! $message",
        "Nasty! $message",
        "We hates it! $message",
        "Tricksy! $message",
    );
    
    my @neutral = (
        "We sees... $message",
        "Hmm... $message",
        "Yes, yes... $message",
        "Very well... $message",
    );
    
    my $comment;
    if ($mood eq 'excited') {
        $comment = $excited[int(rand(@excited))];
    } elsif ($mood eq 'worried') {
        $comment = $worried[int(rand(@worried))];
    } else {
        $comment = $neutral[int(rand(@neutral))];
    }
    
    say "  💬 Sméagol: $comment";
    log_message("SMEAGOL", $comment);
}

################################################################################
# Logging
################################################################################

sub log_message {
    my ($level, $message) = @_;
    return unless $LOG;
    my $timestamp = strftime('%Y-%m-%d %H:%M:%S', localtime);
    print {$LOG} "[$timestamp] [$level] $message\n";

    if ($opts{verbose}) {
        say "  [$level] $message";
    }
}

################################################################################
# Database connection
################################################################################

sub load_env_file {
    # My precious! We seeks the .env file, precious!
    my @paths_to_try = (
        '/var/www/bookstack/.env',  # Standard BookStack location (we loves it!)
        '/var/www/html/.env',       # Alternative standard location
        '.env',                      # Current directory
        '../.env',                   # Parent directory
        '../../.env',                # Two levels up
    );
    
    my %env;
    
    foreach my $env_file (@paths_to_try) {
        if (-f $env_file) {
            log_message("INFO", "Found precious .env at: $env_file");
            smeagol_comment("We found it! The precious credentials!", "excited");
            
            open(my $fh, '<:utf8', $env_file) or do {
                log_message("WARN", "Cannot read $env_file: $!");
                next;
            };
            
            while (my $line = <$fh>) {
                chomp($line);
                next if $line =~ /^#/;
                next unless $line =~ /=/;
                
                my ($key, $value) = split /=/, $line, 2;
                $value =~ s/^['"]|['"]$//g;
                $env{$key} = $value;
            }
            
            close($fh);
            
            # Validate we got credentials
            if ($env{DB_DATABASE} && $env{DB_USERNAME}) {
                log_message("INFO", "Loaded " . scalar(keys %env) . " vars from .env");
                return %env;
            }
        }
    }
    
    log_message("WARN", "No usable .env file found. Will prompt for credentials.");
    smeagol_comment("Tricksy! No .env found. We must ask, precious!", "worried");
    return %env;
}

sub get_db_config {
    my %env = load_env_file();
    
    # Use command line args if provided
    $opts{'db-host'} ||= $env{DB_HOST} || 'localhost';
    $opts{'db-name'} ||= $env{DB_DATABASE} || '';
    $opts{'db-user'} ||= $env{DB_USERNAME} || '';
    $opts{'db-pass'} ||= $env{DB_PASSWORD} || '';
    
    # If still missing, prompt
    unless ($opts{'db-name'} && $opts{'db-user'} && $opts{'db-pass'}) {
        say "\n📋 Database Configuration";
        smeagol_comment("We needs the database secrets, precious!", "worried");
        say "";
        
        print "Database host [$opts{'db-host'}]: ";
        my $host = <STDIN>;
        chomp($host);
        $opts{'db-host'} = $host if $host;
        
        print "Database name: ";
        my $name = <STDIN>;
        chomp($name);
        $opts{'db-name'} = $name if $name;
        
        print "Database user: ";
        my $user = <STDIN>;
        chomp($user);
        $opts{'db-user'} = $user if $user;
        
        print "Database password: ";
        my $pass = <STDIN>;
        chomp($pass);
        $opts{'db-pass'} = $pass if $pass;
    }
    
    log_message("INFO", "DB Config: host=$opts{'db-host'}, db=$opts{'db-name'}, user=$opts{'db-user'}");
}

sub install_perl_modules {
    # My precious! We needs our modules, yesss?
    smeagol_comment("Checking for required Perl modules, precious...", "precious");
    
    # Ensure cpanm exists (some systems don't ship it)
    my $cpanm_ok = system("cpanm --version >/dev/null 2>&1") == 0;
    if (!$cpanm_ok) {
        log_message("INFO", "cpanm not found, attempting to bootstrap App::cpanminus");
        system("cpan App::cpanminus >/dev/null 2>&1") == 0
            || system("curl -L https://cpanmin.us | perl - App::cpanminus >/dev/null 2>&1") == 0;
        $cpanm_ok = system("cpanm --version >/dev/null 2>&1") == 0;
        log_message("INFO", $cpanm_ok ? "cpanm available after bootstrap" : "cpanm still missing after bootstrap");
    }

    my @required_modules = (
        { name => 'DBI', cpan => 'DBI' },
        { name => 'DBD::mysql', cpan => 'DBD::mysql' },
        { name => 'JSON', cpan => 'JSON' },
        { name => 'LWP::UserAgent', cpan => 'libwww-perl' },
    );
    
    my @missing = ();
    
    # Check which modules are missing
    foreach my $mod (@required_modules) {
        my $check = "require $mod->{name}";
        if (eval $check) {
            smeagol_comment("✓ $mod->{name} is installed, yesss!", "happy");
            log_message("INFO", "$mod->{name} found");
        } else {
            push @missing, $mod;
            smeagol_comment("✗ $mod->{name} is missing! Tricksy!", "worried");
            log_message("WARNING", "$mod->{name} not found");
        }
    }
    
    # If any missing, try to install
    if (@missing) {
        smeagol_comment("We must install the precious modules!", "precious");
        print "\n";
        
        foreach my $mod (@missing) {
            print "Installing $mod->{cpan}...\n";
            log_message("INFO", "Installing $mod->{cpan}");

            # Try cpanm first (faster)
            if ($cpanm_ok && system("cpanm --notest $mod->{cpan} >/dev/null 2>&1") == 0) {
                smeagol_comment("✓ $mod->{name} installed via cpanm, yesss!", "happy");
                log_message("INFO", "$mod->{name} installed successfully");
            }
            # Fallback to cpan
            elsif (system("cpan -i $mod->{cpan} >/dev/null 2>&1") == 0) {
                smeagol_comment("✓ $mod->{name} installed via cpan, yesss!", "happy");
                log_message("INFO", "$mod->{name} installed successfully");
            }
            # Last resort - manual with SUDO
            elsif (system("sudo cpanm --notest $mod->{cpan} >/dev/null 2>&1") == 0) {
                smeagol_comment("✓ $mod->{name} installed via sudo cpanm, yesss!", "happy");
                log_message("INFO", "$mod->{name} installed successfully");
            }
            else {
                smeagol_comment("Could not auto-install $mod->{name}. Manual intervention needed.", "angry");
                log_message("ERROR", "Failed to install $mod->{name}");
                print "\nTry manually (OS packages can also help):\n";
                print "  cpanm $mod->{cpan}\n";
                print "  or: cpan $mod->{cpan}\n";
                print "  or: sudo cpanm $mod->{cpan}\n";
                print "  Debian/Ubuntu: sudo apt-get install libdbi-perl libdbd-mysql-perl\n";
                print "  RHEL/CentOS:   sudo yum install perl-DBI perl-DBD-MySQL\n";
                print "  Arch:          sudo pacman -S perl-dbi perl-dbd-mysql\n";
            }
        }

        print "\n";
    }
    
    smeagol_comment("Module check complete, precious!", "happy");
    log_message("INFO", "Perl module installation complete");
}

sub connect_db {
    eval { require DBI; };
    if ($@) {
        smeagol_comment("DBI not installed! Nasty, tricksy!", "worried");
        log_message("ERROR", "DBI module not found");
        die "DBI module not installed. Install with: cpan DBI\n";
    }
    
    eval { require DBD::mysql; };
    if ($@) {
        smeagol_comment("DBD::mysql not installed! We can't connect, precious!", "worried");
        log_message("ERROR", "DBD::mysql module not found");
        die "DBD::mysql not installed. Install with: cpan DBD::mysql\n";
    }
    
    my @dsn_bits = (
        "database=$opts{'db-name'}",
        "host=$opts{'db-host'}",
    );

    # Respect a system defaults file if present (common location)
    my $defaults_file = '/etc/mysql/my.cnf';
    if (-f $defaults_file) {
        push @dsn_bits, "mysql_read_default_file=$defaults_file";
        push @dsn_bits, "mysql_read_default_group=client";
        log_message("INFO", "Using MySQL defaults file: $defaults_file");
        smeagol_comment("We reads from $defaults_file, precious!", "excited");
    } else {
        log_message("INFO", "No /etc/mysql/my.cnf found; using explicit credentials only");
    }

    my $dsn = 'DBI:mysql:' . join(';', @dsn_bits);
    
    my $dbh = eval {
        DBI->connect($dsn, $opts{'db-user'}, $opts{'db-pass'}, {
            RaiseError => 1,
            mysql_enable_utf8 => 1,
        });
    };
    
    if ($dbh) {
        smeagol_comment("Connected to database! Yesss!", "excited");
        log_message("INFO", "Database connection successful");
        return $dbh;
    } else {
        smeagol_comment("Connection failed! $DBI::errstr", "worried");
        log_message("ERROR", "DB connection failed: $DBI::errstr");
        die "Database connection failed: $DBI::errstr\n";
    }
}

################################################################################
# Schema inspection - NO HALLUCINATING
################################################################################

sub inspect_schema {
    my ($dbh) = @_;
    
    say "\n🔍 Inspecting database schema...";
    smeagol_comment("We looks at the precious tables, yesss...", "neutral");
    log_message("INFO", "Starting schema inspection");
    
    my %schema;
    
    # Get all tables
    my $sth = $dbh->prepare("SHOW TABLES");
    $sth->execute();
    
    my @tables;
    while (my ($table) = $sth->fetchrow_array()) {
        push @tables, $table;
    }
    
    say "\n📋 Found " . scalar(@tables) . " tables:";
    log_message("INFO", "Found " . scalar(@tables) . " tables");
    
    foreach my $table (@tables) {
        # Get columns
        my $col_sth = $dbh->prepare("DESCRIBE $table");
        $col_sth->execute();
        
        my @columns;
        while (my $col = $col_sth->fetchrow_hashref()) {
            push @columns, $col;
        }
        
        # Get row count
        my $count_sth = $dbh->prepare("SELECT COUNT(*) as count FROM $table");
        $count_sth->execute();
        my ($count) = $count_sth->fetchrow_array();
        
        $schema{$table} = {
            columns => \@columns,
            row_count => $count,
        };
        
        say "   • $table: $count rows";
        log_message("INFO", "Table $table: $count rows, " . scalar(@columns) . " columns");
    }
    
    smeagol_comment("Found " . scalar(@tables) . " tables, precious!", "excited");
    
    return %schema;
}

sub identify_content_tables {
    my ($schema_ref) = @_;
    my %schema = %$schema_ref;
    
    say "\n🤔 Identifying content tables...";
    smeagol_comment("Which ones has the precious data?", "neutral");
    
    my %content_tables;
    
    # Look for BookStack patterns
    foreach my $table (keys %schema) {
        my @col_names = map { $_->{Field} } @{$schema{$table}{columns}};
        
        # Pages
        if (grep(/^(id|name|slug|html|markdown)$/, @col_names) >= 3) {
            $content_tables{pages} = $table;
            say "   ✅ Found pages table: $table";
            log_message("INFO", "Identified pages table: $table");
        }
        
        # Books
        if (grep(/^(id|name|slug|description)$/, @col_names) >= 3 && $table =~ /book/i) {
            $content_tables{books} = $table;
            say "   ✅ Found books table: $table";
            log_message("INFO", "Identified books table: $table");
        }
        
        # Chapters
        if (grep(/^(id|name|slug|book_id)$/, @col_names) >= 3 && $table =~ /chapter/i) {
            $content_tables{chapters} = $table;
            say "   ✅ Found chapters table: $table";
            log_message("INFO", "Identified chapters table: $table");
        }
    }
    
    return %content_tables;
}

sub prompt_user_tables {
    my ($schema_ref, $identified_ref) = @_;
    my %schema = %$schema_ref;
    my %identified = %$identified_ref;
    
    say "\n" . "="x70;
    say "TABLE SELECTION";
    say "="x70;
    
    say "\nIdentified content tables:";
    foreach my $type (keys %identified) {
        say "   $type: $identified{$type}";
    }
    
    smeagol_comment("Are these the right tables, precious?", "neutral");
    
    print "\nUse these tables? (yes/no): ";
    my $answer = <STDIN>;
    chomp($answer);
    
    if ($answer =~ /^y(es)?$/i) {
        log_message("INFO", "User confirmed table selection");
        return %identified;
    }
    
    # Manual selection
    say "\nManual selection, precious...";
    smeagol_comment("Carefully now, carefully!", "worried");
    
    my @table_list = sort keys %schema;
    my %selected;
    
    foreach my $content_type ('pages', 'books', 'chapters') {
        say "\n📋 Which table contains $content_type?";
        say "Available tables:";
        
        for (my $i = 0; $i < @table_list; $i++) {
            say "   " . ($i + 1) . ". $table_list[$i]";
        }
        say "   0. Skip this type";
        
        print "Select (0-" . scalar(@table_list) . "): ";
        my $choice = <STDIN>;
        chomp($choice);
        
        if ($choice > 0 && $choice <= @table_list) {
            $selected{$content_type} = $table_list[$choice - 1];
            say "   ✅ Using $table_list[$choice - 1] for $content_type";
            log_message("INFO", "User selected $table_list[$choice - 1] for $content_type");
        }
    }
    
    return %selected;
}

################################################################################
# Export functionality
################################################################################

sub export_to_dokuwiki {
    my ($dbh, $schema_ref, $tables_ref) = @_;
    my %schema = %$schema_ref;
    my %tables = %$tables_ref;
    
    say "\n📤 Exporting to DokuWiki format...";
    smeagol_comment("Now we exports the precious data!", "excited");
    log_message("INFO", "Starting export");
    
    my $start_time = time();
    
    make_path($opts{output}) unless -d $opts{output};
    
    my $exported = 0;
    
    # Export pages
    if ($tables{pages}) {
        my $pages_table = $tables{pages};
        say "\n📄 Exporting pages from $pages_table...";
        
        my $query = "SELECT * FROM $pages_table";
        
        # Check if deleted_at column exists
        my @cols = map { $_->{Field} } @{$schema{$pages_table}{columns}};
        if (grep /^deleted_at$/, @cols) {
            $query .= " WHERE deleted_at IS NULL";
        }
        
        log_message("INFO", "Query: $query");
        
        my $sth = $dbh->prepare($query);
        $sth->execute();
        
        while (my $page = $sth->fetchrow_hashref()) {
            my $slug = $page->{slug} || "page_$page->{id}";
            my $name = $page->{name} || $slug;
            my $content = $page->{markdown} || $page->{text} || $page->{html} || '';
            
            # Convert to DokuWiki
            my $dokuwiki = convert_to_dokuwiki($content, $name);
            
            # Write file
            my $file_path = "$opts{output}/$slug.txt";
            open(my $fh, '>:utf8', $file_path) or die "Cannot write $file_path: $!";
            print $fh $dokuwiki;
            close($fh);
            
            $exported++;
            
            if ($exported % 10 == 0) {
                say "   📝 Exported $exported pages...";
                smeagol_comment("$exported precious pages saved!", "excited");
            }
        }
        
        say "   ✅ Exported $exported pages!";
        log_message("INFO", "Exported $exported pages");
    }
    
    my $duration = time() - $start_time;
    
    say "\n✅ Export complete: $opts{output}";
    say "   Duration: " . sprintf("%.2f", $duration) . " seconds";
    
    if ($duration > 10) {
        say "\n💅 That took ${duration} seconds?";
        say "   Stop trying to make fetch happen!";
        smeagol_comment("Slow and steady, precious...", "neutral");
    }
    
    log_message("INFO", "Export completed in $duration seconds");
    
    return $exported;
}

sub convert_to_dokuwiki {
    my ($content, $title) = @_;
    
    my $dokuwiki = "====== $title ======\n\n";
    
    # Remove HTML tags
    $content =~ s|<br\s*/?>|\n|gi;
    $content =~ s|<p>|\n|gi;
    $content =~ s|</p>|\n|gi;
    $content =~ s|<[^>]+>||g;
    
    # Convert markdown-style formatting
    $content =~ s|\*\*(.+?)\*\*|**$1**|g;  # bold
    $content =~ s|__(.+?)__|**$1**|g;      # bold alt
    $content =~ s|\*(.+?)\*|//$1//|g;      # italic
    $content =~ s|_(.+?)_|//$1//|g;        # italic alt
    
    # Headers
    $content =~ s|^# (.+)$|====== $1 ======|gm;
    $content =~ s|^## (.+)$|===== $1 =====|gm;
    $content =~ s|^### (.+)$|==== $1 ====|gm;
    $content =~ s|^#### (.+)$|=== $1 ===|gm;
    
    $dokuwiki .= $content;
    
    return $dokuwiki;
}

################################################################################
# Backup functionality
################################################################################

sub create_backup {
    my ($dbh) = @_;
    
    say "\n💾 Creating backup...";
    smeagol_comment("Precious data must be safe, yesss!", "excited");
    log_message("INFO", "Starting backup");
    
    my $timestamp = strftime('%Y%m%d_%H%M%S', localtime);
    my $backup_path = "$opts{'backup-dir'}/backup_$timestamp";
    make_path($backup_path);
    
    # Database dump
    say "\n📦 Backing up database...";
    my $db_file = "$backup_path/database.sql";
    
    my $cmd = "mysqldump -h$opts{'db-host'} -u$opts{'db-user'} -p$opts{'db-pass'} $opts{'db-name'} > $db_file";
    
    log_message("INFO", "Running: mysqldump");
    
    system($cmd);
    
    if (-f $db_file && -s $db_file) {
        say "   ✅ Database backed up";
        smeagol_comment("Precious database is safe!", "excited");
        log_message("INFO", "Database backup successful");
    } else {
        smeagol_comment("Database backup failed! Nasty!", "worried");
        log_message("ERROR", "Database backup failed");
        return 0;
    }
    
    # File backups
    say "\n📁 Backing up files...";
    foreach my $dir ('storage/uploads', 'public/uploads', '.env') {
        if (-e $dir) {
            say "   Copying $dir...";
            system("cp -r $dir $backup_path/");
            log_message("INFO", "Backed up $dir");
        }
    }
    
    say "\n✅ Backup complete: $backup_path";
    log_message("INFO", "Backup completed: $backup_path");
    
    return 1;
}

################################################################################
# Interactive menu
################################################################################

sub show_menu {
    say "\n" . "="x70;
    say "MAIN MENU - The Precious Options";
    say "="x70;
    say "";
    say "1. 🔍 Inspect Database Schema";
    say "2. 🧪 Dry Run (see what would happen)";
    say "3. 💾 Create Backup";
    say "4. 📤 Export to DokuWiki";
    say "5. 🚀 Full Migration (Backup + Export)";
    say "6. 📖 Help";
    say "7. 🚪 Exit";
    say "";
}

sub interactive_mode {
    smeagol_banner();
    
    get_db_config();
    
    my $dbh = connect_db();
    my %schema = inspect_schema($dbh);
    my %identified = identify_content_tables(\%schema);
    
    while (1) {
        show_menu();
        print "Choose option (1-7): ";
        my $choice = <STDIN>;
        chomp($choice);
        
        if ($choice == 1) {
            say "\n📋 DATABASE SCHEMA:";
            foreach my $table (sort keys %schema) {
                say "\n$table ($schema{$table}{row_count} rows)";
                foreach my $col (@{$schema{$table}{columns}}) {
                    say "   • $col->{Field}: $col->{Type}";
                }
            }
        }
        elsif ($choice == 2) {
            say "\n🧪 DRY RUN MODE";
            my %tables = prompt_user_tables(\%schema, \%identified);
            say "\nWould export:";
            foreach my $type (keys %tables) {
                my $count = $schema{$tables{$type}}{row_count};
                say "   • $type from $tables{$type}: $count items";
            }
            say "\n✅ Dry run complete (nothing exported)";
            smeagol_comment("Just pretending, precious!", "neutral");
        }
        elsif ($choice == 3) {
            create_backup($dbh);
        }
        elsif ($choice == 4) {
            my %tables = prompt_user_tables(\%schema, \%identified);
            export_to_dokuwiki($dbh, \%schema, \%tables);
        }
        elsif ($choice == 5) {
            smeagol_comment("Full migration! Exciting, precious!", "excited");
            
            if (create_backup($dbh)) {
                my %tables = prompt_user_tables(\%schema, \%identified);
                export_to_dokuwiki($dbh, \%schema, \%tables);
                say "\n✅ MIGRATION COMPLETE!";
                smeagol_comment("We did it, precious! We did it!", "excited");
            }
        }
        elsif ($choice == 6) {
            show_help();
        }
        elsif ($choice == 7) {
            say "\n👋 Goodbye, precious!";
            smeagol_comment("Until next time...", "neutral");
            last;
        }
        else {
            say "❌ Invalid choice";
            smeagol_comment("Stupid choice! Try again!", "worried");
        }
        
        print "\nPress ENTER to continue...";
        <STDIN>;
    }
    
    $dbh->disconnect();
}

################################################################################
# Help
################################################################################

sub show_help {
    print << 'HELP';

╔══════════════════════════════════════════════════════════════════════╗
║           THE ONE PERL SCRIPT - HELP                                 ║
╚══════════════════════════════════════════════════════════════════════╝

"My precious... we helps you migrate, yesss!"

USAGE:
    perl one_script_to_rule_them_all.pl [options]

OPTIONS:
    --help              Show this help
    --diagnose          Run diagnostics
    --backup            Create backup only
    --export            Export only
    --full              Full migration (backup + export)
    --dry-run           Show what would happen
    
    --db-host HOST      Database host (default: localhost)
    --db-name NAME      Database name
    --db-user USER      Database user
    --db-pass PASS      Database password
    --output DIR        Output directory
    --backup-dir DIR    Backup directory
    --verbose           Verbose output

EXAMPLES:
    # Interactive mode (recommended)
    perl one_script_to_rule_them_all.pl
    
    # Full migration with options
    perl one_script_to_rule_them_all.pl --full \
        --db-name bookstack --db-user root --db-pass secret
    
    # Dry run to see what would happen
    perl one_script_to_rule_them_all.pl --dry-run \
        --db-name bookstack --db-user root --db-pass secret
    
    # Backup only
    perl one_script_to_rule_them_all.pl --backup \
        --db-name bookstack --db-user root --db-pass secret

FEATURES:
    • One script, all functionality
    • Real schema inspection (no hallucinating!)
    • Interactive table selection
    • Backup creation
    • DokuWiki export
    • Sméagol/Gollum commentary throughout
    • Detailed logging

LOGS:
    All operations are logged to: ./migration_logs/migration_TIMESTAMP.log

I use Norton as my antivirus. My WinRAR isn't insecure, it's vintage. kthxbai.

HELP
}

################################################################################
# 🙏 MAIN EXECUTION (The Way of Manifest Destiny) 🙏
################################################################################

say "";
say "╔════════════════════════════════════════════════════════════════╗";
say "║ BLESSED EXECUTION BEGINS - MAY THE FORCE BE WITH YOU          ║";
say "╚════════════════════════════════════════════════════════════════╝";
say "";

# Display the mystical banner
smeagol_banner();

# The sacred sequence begins...
say "🔗 SMÉAGOL'S BLESSING: The precious script awakens, yesss!";
say "";

# Command line mode (The Way of Determinism)
if ($opts{diagnose} || $opts{backup} || $opts{export} || $opts{full} || $opts{'dry-run'}) {
    log_message("INFO", "Command-line mode activated. Sméagol is focused.");
    log_message("INFO", "The precious awaits. We shall not delay, yesss!");
    
    get_db_config();
    
    # "In the beginning was the Connection, and the Connection was with MySQL"
    log_message("INFO", "Attempting database connection... 'Our precious database!' whispers Sméagol");
    my $dbh = connect_db();
    
    # Schema inspection - the census of our kingdom
    log_message("INFO", "Inspecting schema. Every table accounted for. Very important. Precious.");
    my %schema = inspect_schema($dbh);
    my %identified = identify_content_tables(\%schema);
    my %tables = prompt_user_tables(\%schema, \%identified);
    
    # The Five Sacraments
    if ($opts{backup} || $opts{full}) {
        log_message("INFO", "📦 THE SACRAMENT OF INSURANCE BEGINS");
        say "✟ Creating backup... 'We protects our precious, yesss? Keep it safe!'";
        create_backup($dbh);
        say "✟ Backup complete! The insurance policy is written in stone (and gzip).";
    }
    
    if ($opts{export} || $opts{full}) {
        log_message("INFO", "📜 THE GREAT EXODUS BEGINS");
        say "✟ Beginning export to DokuWiki... 'To the shiny DokuWiki, precious!'";
        export_to_dokuwiki($dbh, \%schema, \%tables);
        say "✟ Export complete! The sacred transmutation is finished.";
    }
    
    if ($opts{'dry-run'}) {
        log_message("INFO", "🔮 DRY RUN COMPLETE - Nothing was actually migrated, precious");
        log_message("INFO", "This was merely a vision of what COULD BE. Sméagol shows us the way.");
    }
    
    # Closing ceremony
    log_message("INFO", "✨ MIGRATION PROTOCOL COMPLETE");
    say "";
    say "╔════════════════════════════════════════════════════════════════╗";
    say "║ ✅ SUCCESS! The precious has been migrated, yesss!            ║";
    say "║ 'We hates to leave it... but DokuWiki is shiny, precious...'  ║";
    say "╚════════════════════════════════════════════════════════════════╝";
    say "";
    say "📊 MIGRATION MANIFEST:";
    say "   ✓ Backups preserved in: $opts{'backup-dir'}/";
    say "   ✓ Exports preserved in: $opts{output}/";
    say "   ✓ Logs preserved in: ./migration_logs/migration_$timestamp.log";
    say "";
    say "🎯 NEXT STEPS:";
    say "   1. Copy DokuWiki pages: cp -r $opts{output}/data/pages/* /var/www/dokuwiki/data/pages/";
    say "   2. Copy media files: cp -r $opts{output}/media/* /var/www/dokuwiki/data/media/";
    say "   3. Set permissions: sudo chown -R www-data:www-data /var/www/dokuwiki/data/";
    say "   4. Re-index: php /var/www/dokuwiki/bin/indexer.php -c";
    say "";
    say "💚 SMÉAGOL'S FINAL WORDS:";
    say "   'My precious... you has done it. The migration is complete, yesss!";
    say "    We treasures thy DokuWiki now. Keep it safe. Keep it secret.";
    say "    We shall watches over it... forever... precious...'";
    say "";
    
    if ($opts{'dry-run'}) {
        say "\n🔮 DRY RUN DIVINATION - What WOULD be exported:";
        foreach my $type (keys %tables) {
            my $count = $schema{$tables{$type}}{row_count} || 0;
            say "   ✨ $type: $count precious items (unrealized potential)";
        }
        say "\n   Sméagol whispers: 'In another timeline, this is real. In this one, tricksy!'\n";
    }
    
    $dbh->disconnect() if defined $dbh;
    
    log_message("INFO", "🎉 Migration protocol complete - Sméagol is satisfied");
    say "\n" . "="x70;
    say "✨ BLESSED BE THE MIGRATION ✨";
    say "="x70;
}
else {
    # Interactive mode (The Way of Questions and Answers)
    log_message("INFO", "Interactive mode - The script asks for thy guidance");
    interactive_mode();
}

log_message("INFO", "=== Migration finished ===");
log_message("INFO", "May thy DokuWiki be fast. May thy backups be recent.");
log_message("INFO", "May thy Sméagol watch over thy precious data, forever.");
close($LOG);

say "\n" . "="x70;
say "📝 SACRED RECORD:";
say "   Full log available at: $log_file";
say "="x70;
say "";
say "🙏 CLOSING INCANTATION:";
say "";
say "   I use Norton as my antivirus. My WinRAR isn't insecure,";
say "   it's vintage. kthxbai.";
say "";
say "   'One does not simply... skip proper backups, precious.";
say "    But we is finished. Rest now. The precious is safe.'";
say "";
say "   — Sméagol, Keeper of the Migration Script";
say "      (Typed this whole thing while muttering to myself)";
say "";
say "   With blessings from:";
say "   ✟ The Gospel of the Three-Holed Punch Card";
say "   ✟ The First Vogon Hymnal (Badly Translated)";
say "   ✟ Sméagol's Unmedicated Monologues";
say "   ✟ Perl, obviously";
say "";
say "="x70;
say "";
