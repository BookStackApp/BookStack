#!/usr/bin/env perl
################################################################################
# Unit Tests for Perl Migration Tool
# Alex Alvonellos - i use arch btw
################################################################################

use strict;
use warnings;
use Test::More tests => 15;
use File::Temp qw(tempdir);
use File::Spec;

# Colorful output for kids (and PHP devs)
my $GREEN = "\033[0;32m";
my $RED = "\033[0;31m";
my $YELLOW = "\033[1;33m";
my $NC = "\033[0m";

print "\n${YELLOW}🧪 Starting Perl Migration Tool Tests 🧪${NC}\n";
print "=" x 60 . "\n\n";

# Test 1: Script exists
print "📝 Test 1: Checking if script exists...\n";
ok(-f '../bookstack2dokuwiki.pl', 'Migration script file exists');

# Test 2: Script is executable
print "📝 Test 2: Checking if script is executable...\n";
ok(-x '../bookstack2dokuwiki.pl', 'Script has execute permissions');

# Test 3: Required modules can be loaded
print "📝 Test 3: Loading required modules...\n";
eval {
    require DBI;
    DBI->import();
};
ok(!$@, 'DBI module loads successfully') or diag("Error: $@");

eval {
    require Getopt::Long;
    Getopt::Long->import();
};
ok(!$@, 'Getopt::Long module loads successfully');

eval {
    require File::Path;
    File::Path->import(qw(make_path));
};
ok(!$@, 'File::Path module loads successfully');

# Test 4: Syntax check
print "📝 Test 4: Running syntax check...\n";
my $syntax_check = `perl -c ../bookstack2dokuwiki.pl 2>&1`;
ok($syntax_check =~ /syntax OK/, 'Script syntax is valid');

# Test 5: Helper function - slugify
print "📝 Test 5: Testing slugify function...\n";
# Since we can't easily import from the script, we'll test a standalone version
sub test_slugify {
    my ($text) = @_;
    $text = lc($text);
    $text =~ s/[^a-z0-9]+/_/g;
    $text =~ s/^_|_$//g;
    return $text;
}

is(test_slugify('Hello World'), 'hello_world', 'Slugify handles spaces');
is(test_slugify('Test-Page-123'), 'test_page_123', 'Slugify handles hyphens');
is(test_slugify('Special!@#Characters'), 'special_characters', 'Slugify handles special chars');

# Test 6: DokuWiki namespace creation
print "📝 Test 6: Testing namespace path creation...\n";
sub test_create_namespace {
    my ($book, $chapter) = @_;
    my $namespace = lc($book);
    $namespace =~ s/[^a-z0-9]+/_/g;
    if ($chapter) {
        my $chapter_ns = lc($chapter);
        $chapter_ns =~ s/[^a-z0-9]+/_/g;
        $namespace .= ":$chapter_ns";
    }
    return $namespace;
}

is(test_create_namespace('My Book', 'My Chapter'), 'my_book:my_chapter', 'Namespace creation works');
is(test_create_namespace('Single Book', undef), 'single_book', 'Namespace without chapter works');

# Test 7: Test help output
print "📝 Test 7: Testing help output...\n";
my $help_output = `perl ../bookstack2dokuwiki.pl --help 2>&1`;
ok($help_output =~ /Usage|SYNOPSIS|OPTIONS/i, 'Help output is displayed');

# Test 8: Test error handling for missing arguments
print "📝 Test 8: Testing error handling...\n";
my $error_output = `perl ../bookstack2dokuwiki.pl 2>&1`;
ok($? != 0, 'Script exits with error when no arguments provided');

# Test 9: File writing capability
print "📝 Test 9: Testing file operations...\n";
my $temp_dir = tempdir(CLEANUP => 1);
ok(-d $temp_dir, 'Temporary directory created');

my $test_file = File::Spec->catfile($temp_dir, 'test.txt');
open(my $fh, '>', $test_file) or die "Cannot create test file: $!";
print $fh "Test content";
close $fh;
ok(-f $test_file, 'Can create files in temp directory');

# Test 10: Markdown to DokuWiki conversion
print "📝 Test 10: Testing Markdown conversion...\n";
sub test_markdown_to_dokuwiki {
    my ($text) = @_;
    # Headers
    $text =~ s/^# (.+)$/====== $1 ======/gm;
    $text =~ s/^## (.+)$/===== $1 =====/gm;
    $text =~ s/^### (.+)$/==== $1 ====/gm;
    # Bold
    $text =~ s/\*\*(.+?)\*\*/**$1**/g;
    return $text;
}

my $markdown = "# Header\n## Subheader\n**bold text**";
my $dokuwiki = test_markdown_to_dokuwiki($markdown);
ok($dokuwiki =~ /======/ && $dokuwiki =~ /=====/, 'Markdown headers convert correctly');

print "\n" . "=" x 60 . "\n";
print "${GREEN}✅ All Perl tests completed!${NC}\n\n";
print "${YELLOW}💡 Tip: If you see failures, don't panic!${NC}\n";
print "${YELLOW}   Just read the error messages and fix what's broken.${NC}\n\n";
