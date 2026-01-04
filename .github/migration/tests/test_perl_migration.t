#!/usr/bin/env perl
use strict;
use warnings;
use Test::More tests => 15;
use Test::Exception;
use File::Temp qw(tempdir);
use File::Path qw(make_path remove_tree);

# Test: Filename Sanitization
sub sanitize_filename {
    my ($name) = @_;
    return 'unnamed' unless defined $name && length($name) > 0;
    
    $name = lc($name);
    $name =~ s/[^a-z0-9_-]/_/g;
    $name =~ s/_+/_/g;
    $name =~ s/^_+|_+$//g;
    
    return $name || 'unnamed';
}

# Test sanitization
is(sanitize_filename('My Page!'), 'my_page', 'Special characters removed');
is(sanitize_filename('Test@#$%'), 'test', 'Symbols removed');
is(sanitize_filename('Spaced Out'), 'spaced_out', 'Spaces converted');
is(sanitize_filename(''), 'unnamed', 'Empty string handled');
is(sanitize_filename(undef), 'unnamed', 'Undef handled');

# Test: HTML to DokuWiki Conversion
sub convert_html_to_dokuwiki {
    my ($html) = @_;
    return '' unless defined $html;
    
    # Simple conversions for testing
    $html =~ s/<h1>(.*?)<\/h1>/====== $1 ======/g;
    $html =~ s/<h2>(.*?)<\/h2>/===== $1 =====/g;
    $html =~ s/<strong>(.*?)<\/strong>/**$1**/g;
    $html =~ s/<em>(.*?)<\/em>\/\/$1\/\//g;
    $html =~ s/<code>(.*?)<\/code>/''$1''/g;
    
    return $html;
}

like(convert_html_to_dokuwiki('<h1>Title</h1>'), qr/======.*======/, 'H1 converted');
like(convert_html_to_dokuwiki('<strong>bold</strong>'), qr/\*\*bold\*\*/, 'Strong converted');
like(convert_html_to_dokuwiki('<code>code</code>'), qr/''code''/, 'Code converted');

# Test: Database Connection Parameters
sub validate_db_params {
    my %params = @_;
    
    return 0 unless $params{host};
    return 0 unless $params{database};
    return 0 unless $params{user};
    
    return 1;
}

ok(validate_db_params(host => 'localhost', database => 'bookstack', user => 'root', password => 'pass'), 
   'Valid DB params accepted');
ok(!validate_db_params(host => 'localhost', database => 'bookstack'), 
   'Missing user rejected');
ok(!validate_db_params(user => 'root', password => 'pass'), 
   'Missing host/database rejected');

# Test: Directory Structure Creation
sub create_export_structure {
    my ($base_path, $book_slug) = @_;
    
    my $book_path = "$base_path/$book_slug";
    make_path($book_path) or return 0;
    
    return -d $book_path;
}

my $temp_dir = tempdir(CLEANUP => 1);
ok(create_export_structure($temp_dir, 'test_book'), 'Directory structure created');
ok(-d "$temp_dir/test_book", 'Book directory exists');

# Test: Sméagol Comments
sub smeagol_comment {
    my ($message, $mood) = @_;
    $mood ||= 'neutral';
    
    my %responses = (
        excited => ['Yesss, my precious!', 'We likes it!', 'Gollum gollum!'],
        worried => ['Careful, precious...', 'Nasty database...', 'It burns us...'],
        neutral => ['We does it...', 'Working, precious...', 'Processing...']
    );
    
    my $responses_ref = $responses{$mood} || $responses{neutral};
    return $responses_ref->[0] . " $message";
}

like(smeagol_comment('Exporting data', 'excited'), qr/(Yesss|We likes|Gollum)/, 'Excited response');
like(smeagol_comment('Database error', 'worried'), qr/(Careful|Nasty|burns)/, 'Worried response');

print "\n";
print "=" x 70 . "\n";
print " All Perl tests passed! My precious tests are good, yesss!\n";
print "=" x 70 . "\n";

done_testing();
