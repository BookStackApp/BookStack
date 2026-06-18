# ============================================
# CONFIGURATION
# ============================================

$BookStackUrl = "http://localhost:8088"

$TokenId = "CbM1wBXDSH6qNMh8pGB2phBX7LHgmHPQ"
$TokenSecret = "WttR9gBxXCWZ3Xs3z3bWATZVZIM8X6Cx"

$RootFolder = "/mnt/c/Users/benoi/Documents/BookStack"
$TempFolder = "/home/benoit/BookStack/temp"
$PdfToText = "/usr/bin/pdftohtml" 
#$PdfToText = "/usr/bin/pdftotext" 
$PdfInfo   = "/usr/bin/pdfinfo"
$PdfImages = "/usr/bin/pdfimages"

$Headers = @{
    Authorization = "Token ${TokenId}:${TokenSecret}"
}

# ============================================
# FONCTIONS
# ============================================
# Charger les livres une seule fois
$Global:AllBooks = (Invoke-RestMethod `
    -Method Get `
    -Uri "${BookStackUrl}/api/books?count=500" `
    -Headers $Headers).data

function Get-BookByName
{
    param([string]$BookName)

    return $Global:AllBooks |
        Where-Object {
            $_.name.Trim().ToLower() -eq $BookName.Trim().ToLower()
        } |
        Select-Object -First 1
}

function Invoke-BookStackRequest
{
    param(
        [string]$Method,
        [string]$Uri,
        $Body = $null
    )

    while ($true)
    {
        try
        {
            if ($null -ne $Body)
            {
                return Invoke-RestMethod `
                    -Method $Method `
                    -Uri $Uri `
                    -Headers $Headers `
                    -ContentType "application/json" `
                    -Body $Body
            }
            else
            {
                return Invoke-RestMethod `
                    -Method $Method `
                    -Uri $Uri `
                    -Headers $Headers
            }
        }
        catch
        {
            if ($_.Exception.Response.StatusCode.value__ -eq 429)
            {
                Write-Warning "Limite API atteinte, attente 10 secondes..."
                Start-Sleep -Seconds 10
                continue
            }

            throw
        }
    }
}

function Get-BookByName
{
    param([string]$BookName)
    # count=0 = pas de limite, on récupère tous les livres
    $Books = Invoke-RestMethod -Method Get -Uri "${BookStackUrl}/api/books?count=500" -Headers $Headers
    # Select-Object -First 1 garantit qu'on retourne toujours un objet unique, jamais un tableau
    return $Books.data | Where-Object { $_.name -eq $BookName } | Select-Object -First 1
}

function New-BookStackPage
{
    # Crée une page directement dans un livre (sans chapitre), retourne l'objet page (avec son id)
    param([int]$BookId, [string]$PageName, [string]$Html)
    $Body = @{
        book_id = $BookId
        name    = $PageName
        html    = $Html
    } | ConvertTo-Json -Depth 20

    return Invoke-RestMethod `
        -Method Post `
        -Uri "${BookStackUrl}/api/pages" `
        -Headers $Headers `
        -ContentType "application/json" `
        -Body $Body
}

function Update-BookStackPage
{
    param([int]$PageId, [string]$Html)
    $Body = @{
        html = $Html
    } | ConvertTo-Json -Depth 20

    return Invoke-RestMethod `
        -Method Put `
        -Uri "${BookStackUrl}/api/pages/${PageId}" `
        -Headers $Headers `
        -ContentType "application/json" `
        -Body $Body
}

function Upload-ImageToBookStack
{
    # uploaded_to est obligatoire : ID de la page à laquelle l'image est rattachée
    param([string]$ImagePath, [string]$ImageName, [int]$PageId)

    $Bytes    = [System.IO.File]::ReadAllBytes($ImagePath)
    $Boundary = "----BookStackBoundary$(Get-Random)"
    $Ext      = [System.IO.Path]::GetExtension($ImagePath).ToLower()
    $MimeType = if ($Ext -eq ".png") { "image/png" } else { "image/jpeg" }

    $BodyParts = [System.Collections.Generic.List[byte]]::new()

    $Header  = "--$Boundary`r`nContent-Disposition: form-data; name=`"type`"`r`n`r`ngallery`r`n"
    $Header += "--$Boundary`r`nContent-Disposition: form-data; name=`"name`"`r`n`r`n$ImageName`r`n"
    $Header += "--$Boundary`r`nContent-Disposition: form-data; name=`"uploaded_to`"`r`n`r`n$PageId`r`n"
    $Header += "--$Boundary`r`nContent-Disposition: form-data; name=`"image`"; filename=`"$([System.IO.Path]::GetFileName($ImagePath))`"`r`nContent-Type: $MimeType`r`n`r`n"

    $BodyParts.AddRange([System.Text.Encoding]::UTF8.GetBytes($Header))
    $BodyParts.AddRange($Bytes)
    $BodyParts.AddRange([System.Text.Encoding]::UTF8.GetBytes("`r`n--$Boundary--`r`n"))

    $Response = Invoke-RestMethod `
        -Method Post `
        -Uri "${BookStackUrl}/api/image-gallery" `
        -Headers $Headers `
        -ContentType "multipart/form-data; boundary=$Boundary" `
        -Body $BodyParts.ToArray()

    return $Response.url
}

function Get-PdfPageCount
{
    param([string]$PdfFile)
    $Info = & $PdfInfo $PdfFile
    $Line = $Info | Where-Object { $_ -match "^Pages:" }
    if ($Line -match ":\s*(\d+)") { return [int]$Matches[1] }
    return 0
}

function Build-PdfPageHtml_PdfToHtml
{
    param([string]$PdfFile, [int]$PageNum, [ref]$ImageFiles, [string]$BookName)

    $BookTempDir = Join-Path $TempFolder $BookName
    if (-not (Test-Path $BookTempDir)) {
        New-Item -ItemType Directory -Path $BookTempDir -Force | Out-Null
    }

    # pdftotext au lieu de pdftohtml → HTML propre garanti
    $TempTxt = Join-Path $BookTempDir "page_${PageNum}.txt"
    & /usr/bin/pdftotext -f $PageNum -l $PageNum $PdfFile $TempTxt 2>$null

    $BodyHtml = ""
    if (Test-Path $TempTxt)
    {
        $Lines = Get-Content -Path $TempTxt -Encoding UTF8
        $BodyHtml = ($Lines | ForEach-Object {
            $line = $_.Trim()
            if ($line -eq "") { "<br/>" }
            else { "<p>" + [System.Web.HttpUtility]::HtmlEncode($line) + "</p>" }
        }) -join "`n"
        Remove-Item $TempTxt -Force
    }

    # Images extraites séparément avec pdfimages
    $ImageMap = @{}
    & $PdfImages -f $PageNum -l $PageNum -png $PdfFile (Join-Path $BookTempDir "page_${PageNum}") 2>$null
    Get-ChildItem -Path $BookTempDir -File |
        Where-Object { $_.Extension -match '\.(png|jpg|jpeg)$' -and $_.BaseName -like "page_${PageNum}*" } |
        ForEach-Object {
            $ImageMap[$_.Name] = $_.FullName
            $BodyHtml += "`n<p><img src=`"##IMG:$($_.Name)##`" style=`"max-width:100%`"/></p>"
        }

    $ImageFiles.Value = $ImageMap
    return @{ BodyHtml = $BodyHtml; TempDir = $BookTempDir }
}

function Build-PdfPageHtml
{
    param([string]$PdfFile, [int]$PageNum, [ref]$ImageFiles, [string]$BookName)

    # Sous-dossier par livre
    $BookTempDir = Join-Path $TempFolder $BookName
    if (-not (Test-Path $BookTempDir))
    {
        New-Item -ItemType Directory -Path $BookTempDir -Force | Out-Null
    }

    $TempBase = Join-Path $BookTempDir "page_${PageNum}"

    & $PdfToText -f $PageNum -l $PageNum -noframes $PdfFile $TempBase 2>$null

    $HtmlFile = "${TempBase}.html"

    if (-not (Test-Path $HtmlFile))
    {
        Write-Warning "  pdftohtml n'a pas généré de HTML pour la page $PageNum"
        $ImageFiles.Value = @{}
        return @{ BodyHtml = ""; TempDir = $BookTempDir }
    }

    $RawHtml = Get-Content -Path $HtmlFile -Raw -Encoding UTF8
    Write-Host $RawHtml

    if ($RawHtml -match '(?si)<body[^>]*>(.*?)</body>')
    {
        $BodyHtml = $Matches[1].Trim()
    }
    else
    {
        $BodyHtml = $RawHtml
    }

    $BodyHtml = $BodyHtml -replace '(?si)<a\s[^>]*>\s*(Previous|Next|Pr&#233;c&#233;dent|Suivant)\s*</a>', ''
    # Supprimer le Document Outline généré par pdftohtml
    $BodyHtml = $BodyHtml -replace '(?si)<hr/>\s*<a name="outline"></a>.*?<hr/>', ''
    $BodyHtml = $BodyHtml -replace '(?si)<b>(.*?)</b>', '<h4>$1</h4>'

    $BodyHtml = $BodyHtml -replace '(?si)<p[^>]*style="[^"]*font-size:(\d+)px[^"]*"[^>]*>(.*?)</p>', {
    $size = [int]$_.Groups[1].Value
    $content = $_.Groups[2].Value
    Write-Host $size -ge
    if     ($size -ge 20) { "<h1>$content</h1>" }
    elseif ($size -ge 16) { "<h2>$content</h2>" }
    elseif ($size -ge 14) { "<h3>$content</h3>" }
    elseif ($size -ge 12) { "<h4>$content</h4>" }
    else                  { "<p>$content</p>" }
}
    
    Write-Host $BodyHtml

    $ImageMap = @{}
    Get-ChildItem -Path $BookTempDir -File |
        Where-Object { $_.Extension -match '\.(png|jpg|jpeg)$' -and $_.BaseName -like "page_${PageNum}*" } |
        ForEach-Object { $ImageMap[$_.Name] = $_.FullName }

    foreach ($ShortName in $ImageMap.Keys)
    {
        $BodyHtml = $BodyHtml -replace `
            "(?i)src=`"[^`"]*$([regex]::Escape($ShortName))`"", `
            "src=`"##IMG:$ShortName##`""
    }

    $ImageFiles.Value = $ImageMap

    return @{
        BodyHtml = $BodyHtml
        TempDir  = $BookTempDir
    }
}
function Get-DocxPages
{
    param([string]$DocxFile)

    $PythonScript = @'
import sys
import json
import base64
from docx import Document
from docx.oxml.ns import qn

def escape(text):
    return text.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")

def runs_to_html(para):
    """Convertit les runs d'un paragraphe en HTML inline (gras, italique, souligné)."""
    html = ""
    for run in para.runs:
        text = escape(run.text) if run.text else ""
        if not text:
            continue
        if run.bold:
            text = f"<strong>{text}</strong>"
        if run.italic:
            text = f"<em>{text}</em>"
        if run.underline:
            text = f"<u>{text}</u>"
        html += text
    return html

def table_to_html(table):
    """Convertit un tableau Word en <table> HTML."""
    rows_html = []
    for i, row in enumerate(table.rows):
        cells_html = []
        for cell in row.cells:
            tag  = "th" if i == 0 else "td"
            text = escape(cell.text)
            cells_html.append(f"<{tag}>{text}</{tag}>")
        rows_html.append("<tr>" + "".join(cells_html) + "</tr>")
    return '<table border="1" style="border-collapse:collapse;width:100%">' + "".join(rows_html) + "</table>"

doc = Document(sys.argv[1])
pages = []
current_blocks = []
list_items  = []
list_is_num = False   # True = <ol>, False = <ul>

def flush_list():
    if list_items:
        tag = "ol" if list_is_num else "ul"
        current_blocks.append({"type": "text", "html": f"<{tag}>" + "".join(list_items) + f"</{tag}>"})
        list_items.clear()

# Parcours mixte paragraphes + tableaux dans l'ordre du document
for block in doc.element.body:
    tag_name = block.tag.split('}')[-1]

    # --- TABLEAU ---
    if tag_name == 'tbl':
        from docx.table import Table
        flush_list()
        tbl = Table(block, doc)
        current_blocks.append({"type": "text", "html": table_to_html(tbl)})
        continue

    # --- PARAGRAPHE ---
    if tag_name != 'p':
        continue

    from docx.text.paragraph import Paragraph
    para = Paragraph(block, doc)

    # Détection saut de page
    has_page_break = False
    for run in para.runs:
        for br in run._element.findall('.//' + qn('w:br')):
            if br.get(qn('w:type')) == 'page':
                has_page_break = True
                break
    pPr = para._element.find(qn('w:pPr'))
    if pPr is not None:
        pageBreak = pPr.find(qn('w:pageBreakBefore'))
        if pageBreak is not None and pageBreak.get(qn('w:val'), 'true') != 'false':
            if current_blocks:
                has_page_break = True

    if has_page_break and current_blocks:
        flush_list()
        pages.append(current_blocks)
        current_blocks = []

    # Images dans le paragraphe
    for run in para.runs:
        for drawing in run._element.findall('.//' + qn('a:blip'),
                {'a': 'http://schemas.openxmlformats.org/drawingml/2006/main'}):
            rId = drawing.get('{http://schemas.openxmlformats.org/officeDocument/2006/relationships}embed')
            if rId:
                part = doc.part.related_parts.get(rId)
                if part:
                    ext = part.partname.split('.')[-1].lower()
                    b64 = base64.b64encode(part.blob).decode('utf-8')
                    flush_list()
                    current_blocks.append({"type": "image", "b64": b64, "ext": ext})

    style   = para.style.name if para.style else ''
    inline  = runs_to_html(para)
    if not inline.strip():
        continue

    # Listes à puces / numérotées
    num_pr = pPr.find(qn('w:numPr')) if pPr is not None else None
    is_num = style.startswith('List Number') or style.startswith('List Paragraph')
    if num_pr is not None or style.startswith('List'):
        list_items.append(f"<li>{inline}</li>")
    else:
        flush_list()
        if style.startswith('Heading 1'):
            html = f'<h1>{inline}</h1>'
        elif style.startswith('Heading 2'):
            html = f'<h2>{inline}</h2>'
        elif style.startswith('Heading 3'):
            html = f'<h3>{inline}</h3>'
        elif style.startswith('Heading 4') or style.startswith('Heading 5'):
            html = f'<h4>{inline}</h4>'
        else:
            html = f'<p>{inline}</p>'
        current_blocks.append({"type": "text", "html": html})

flush_list()
if current_blocks:
    pages.append(current_blocks)

print(json.dumps(pages))
'@

    $TempPy = [System.IO.Path]::GetTempFileName() + ".py"
    Set-Content -Path $TempPy -Value $PythonScript -Encoding UTF8

    try
    {
        $Json  = python3 $TempPy $DocxFile
        $Pages = $Json | ConvertFrom-Json
        return $Pages
    }
    finally
    {
        Remove-Item $TempPy -Force
    }
}

function Remove-BookStackPageIfExists
{
    param([int]$BookId, [string]$PageName)

    try
    {
        # Récupérer le livre avec ses pages
        $BookDetails = Invoke-BookStackRequest `
            -Method Get `
            -Uri    "${BookStackUrl}/api/books/${BookId}"

        $Existing = $BookDetails.contents |
            Where-Object { $_.type -eq "page" -and $_.name -eq $PageName }

        foreach ($P in $Existing)
        {
            Invoke-BookStackRequest `
                -Method Delete `
                -Uri    "${BookStackUrl}/api/pages/$($P.id)"

            Write-Host "    Page supprimée : $($P.name) (ID=$($P.id))"
        }
    }
    catch
    {
        Write-Warning "    Erreur suppression page '$PageName' : $_"
    }
}

function Add-BookStackAttachment
{
    param([int]$PageId, [string]$FilePath, [string]$FileName)

    $Bytes    = [System.IO.File]::ReadAllBytes($FilePath)
    $Boundary = "----BookStackBoundary$(Get-Random)"
    $Ext      = [System.IO.Path]::GetExtension($FilePath).ToLower()
    $MimeType = switch ($Ext)
    {
        ".pdf"  { "application/pdf" }
        ".docx" { "application/vnd.openxmlformats-officedocument.wordprocessingml.document" }
        default { "application/octet-stream" }
    }

    $BodyParts = [System.Collections.Generic.List[byte]]::new()

    $Header  = "--$Boundary`r`nContent-Disposition: form-data; name=`"uploaded_to`"`r`n`r`n$PageId`r`n"
    $Header += "--$Boundary`r`nContent-Disposition: form-data; name=`"name`"`r`n`r`n$FileName`r`n"
    $Header += "--$Boundary`r`nContent-Disposition: form-data; name=`"file`"; filename=`"$([System.IO.Path]::GetFileName($FilePath))`"`r`nContent-Type: $MimeType`r`n`r`n"

    $BodyParts.AddRange([System.Text.Encoding]::UTF8.GetBytes($Header))
    $BodyParts.AddRange($Bytes)
    $BodyParts.AddRange([System.Text.Encoding]::UTF8.GetBytes("`r`n--$Boundary--`r`n"))

    $Response = Invoke-RestMethod `
        -Method Post `
        -Uri "${BookStackUrl}/api/attachments" `
        -Headers $Headers `
        -ContentType "multipart/form-data; boundary=$Boundary" `
        -Body $BodyParts.ToArray()

    return $Response
}

function New-BookStackChapter
{
    param([int]$BookId, [string]$ChapterName)

    $Body = @{
        book_id     = $BookId
        name        = $ChapterName
        description = ""
    } | ConvertTo-Json -Depth 5

    return Invoke-BookStackRequest `
        -Method Post `
        -Uri    "${BookStackUrl}/api/chapters" `
        -Body   $Body
}

function Get-ChapterByName
{
    param([int]$BookId, [string]$ChapterName)

    $Chapters = Invoke-BookStackRequest `
        -Method Get `
        -Uri    "${BookStackUrl}/api/chapters?count=500&filter[book_id]=${BookId}"

    return $Chapters.data |
        Where-Object { $_.name -eq $ChapterName } |
        Select-Object -First 1
}

function New-BookStackPageInChapter
{
    param([int]$BookId, [int]$ChapterId, [string]$PageName, [string]$Html)

    $Body = @{
        book_id    = $BookId
        chapter_id = $ChapterId
        name       = $PageName
        html       = $Html
    } | ConvertTo-Json -Depth 20

    return Invoke-BookStackRequest `
        -Method Post `
        -Uri    "${BookStackUrl}/api/pages" `
        -Body   $Body
}

function Import-FileAsPage
{
    # Importe un fichier PDF ou DOCX comme une page BookStack
    # Retourne l'objet page créé
    param(
        [int]$BookId,
        [int]$ChapterId = 0,
        [string]$PageName,
        [System.IO.FileInfo]$File
    )

    Remove-BookStackPageIfExists -BookId $BookId -PageName $PageName

    if ($ChapterId -gt 0)
    {
        $Page = New-BookStackPageInChapter `
            -BookId    $BookId `
            -ChapterId $ChapterId `
            -PageName  $PageName `
            -Html      "<p>Import en cours...</p>"
    }
    else
    {
        $Page = New-BookStackPage `
            -BookId   $BookId `
            -PageName $PageName `
            -Html     "<p>Import en cours...</p>"
    }

    $FinalParts = @()

    switch ($File.Extension.ToLower())
    {
        ".pdf"
        {
            $PageCount   = Get-PdfPageCount -PdfFile $File.FullName
            $BookTempDir = Join-Path $TempFolder $PageName
            if (-not (Test-Path $BookTempDir))
            {
                New-Item -ItemType Directory -Path $BookTempDir -Force | Out-Null
            }

            for ($i = 1; $i -le $PageCount; $i++)
            {
                Write-Host "    Page PDF $i/$PageCount..."

                $ImageMap = @{}
                $Result   = Build-PdfPageHtml `
                    -PdfFile    $File.FullName `
                    -PageNum    $i `
                    -ImageFiles ([ref]$ImageMap) `
                    -BookName   $PageName

                try
                {
                    if ($i -gt 1) { $FinalParts += "<hr/>" }

                    $PageHtml = $Result.BodyHtml

                    foreach ($ShortName in $ImageMap.Keys)
                    {
                        try
                        {
                            $UploadName = "pdf-p${i}-$([System.IO.Path]::GetFileNameWithoutExtension($ShortName))"
                            $ImgUrl     = Upload-ImageToBookStack `
                                -ImagePath $ImageMap[$ShortName] `
                                -ImageName $UploadName `
                                -PageId    $Page.id

                            $PageHtml = $PageHtml.Replace(
                                "src=`"##IMG:$ShortName##`"",
                                "src=`"$ImgUrl`""
                            )
                            Write-Host "      Image uploadée : $UploadName"
                        }
                        catch { Write-Warning "      Erreur image $ShortName : $_" }
                    }

                    $FinalParts += $PageHtml
                }
                catch { Write-Warning "    Page PDF $i : $_" }
            }

            if (Test-Path $BookTempDir)
            {
                Remove-Item -Path $BookTempDir -Recurse -Force
            }
        }

        ".docx"
        {
            $Pages       = Get-DocxPages -DocxFile $File.FullName
            $BookTempDir = Join-Path $TempFolder $PageName
            if (-not (Test-Path $BookTempDir))
            {
                New-Item -ItemType Directory -Path $BookTempDir -Force | Out-Null
            }

            for ($i = 0; $i -lt $Pages.Count; $i++)
            {
                if ($i -gt 0) { $FinalParts += "<hr/>" }

                $ImgIndex = 0
                $ImageUrls = @{}

                foreach ($Block in $Pages[$i])
                {
                    if ($Block.type -eq "image")
                    {
                        $TempImg = Join-Path $BookTempDir ("docx-p$($i+1)-img$ImgIndex." + $Block.ext)
                        try
                        {
                            $ImgBytes = [Convert]::FromBase64String($Block.b64)
                            [System.IO.File]::WriteAllBytes($TempImg, $ImgBytes)

                            $ImgName = "docx-p$($i+1)-img$ImgIndex"
                            $ImgUrl  = Upload-ImageToBookStack `
                                -ImagePath $TempImg `
                                -ImageName $ImgName `
                                -PageId    $Page.id

                            $ImageUrls[$ImgIndex] = "<img src=`"$ImgUrl`" alt=`"$ImgName`" style=`"max-width:100%`"/>"
                            Write-Host "      Image uploadée : $ImgName"
                        }
                        catch { Write-Warning "      Erreur image : $_" }
                        finally
                        {
                            if (Test-Path $TempImg) { Remove-Item $TempImg -Force }
                        }
                        $ImgIndex++
                    }
                }

                $ImgCounter = 0
                foreach ($Block in $Pages[$i])
                {
                    if ($Block.type -eq "text")
                    {
                        $FinalParts += $Block.html
                    }
                    elseif ($Block.type -eq "image")
                    {
                        if ($ImageUrls.ContainsKey($ImgCounter))
                        {
                            $FinalParts += "<p>$($ImageUrls[$ImgCounter])</p>"
                        }
                        $ImgCounter++
                    }
                }
            }

            if (Test-Path $BookTempDir)
            {
                Remove-Item -Path $BookTempDir -Recurse -Force
            }
        }
    }

    $FinalHtml = $FinalParts -join "`n"
    # Envelopper dans un div propre pour BookStack
    $FinalHtml = "<div>" + $FinalHtml + "</div>"
    Update-BookStackPage -PageId $Page.id -Html $FinalHtml

    # Pièce jointe si formulaire
    if ($PageName -match "(?i)formulaire")
    {
        try
        {
            Add-BookStackAttachment `
                -PageId   $Page.id `
                -FilePath $File.FullName `
                -FileName ($PageName + $File.Extension) | Out-Null
            Write-Host "    Pièce jointe ajoutée"
        }
        catch { Write-Warning "    Erreur pièce jointe : $_" }
    }

    return $Page
}

# ============================================
# DEBUT LOG
# ============================================

Start-Transcript `
    -Path "/home/benoit/scripts/BookStack/Import.log" `
    -Append

Write-Host "Début import"

# ============================================
# RECUPERATION DES ETAGERES
# ============================================

$Shelves = Invoke-RestMethod -Method Get -Uri "${BookStackUrl}/api/shelves" -Headers $Headers

# ============================================
# PARCOURS DES DOSSIERS
# ============================================
Get-ChildItem $RootFolder -Directory | ForEach-Object {

    $ShelfFolder = $_
    $ShelfName   = $ShelfFolder.Name

    Write-Host ""
    Write-Host "================================="
    Write-Host "ETAGERE : $ShelfName"
    Write-Host "================================="

    $Shelf = $Shelves.data | Where-Object { $_.name -eq $ShelfName }

    if (-not $Shelf)
    {
        Write-Host "Création étagère : $ShelfName"
        $Body  = @{ name = $ShelfName; description = "" } | ConvertTo-Json
        $Shelf = Invoke-RestMethod `
            -Method Post `
            -Uri "${BookStackUrl}/api/shelves" `
            -Headers $Headers `
            -ContentType "application/json" `
            -Body $Body
    }

    # --------------------------------------------------
    # NIVEAU 1 : fichiers directement dans le dossier étagère
    # → Livre au nom du document, contenu dans description
    # --------------------------------------------------
    Get-ChildItem -Path $ShelfFolder.FullName |
    Where-Object { -not $_.PSIsContainer -and $_.Extension -match '\.(pdf|docx)$' } |
    ForEach-Object {

        $File    = $_
        $DocName = [System.IO.Path]::GetFileNameWithoutExtension($File.Name)

        Write-Host ""
        Write-Host "  [Niveau étagère] Document : $DocName"

        $TagsArray = [System.Collections.Generic.List[object]]::new()
        $TagsArray.Add([pscustomobject]@{ name = $ShelfName; value = "" })

        $Book = Get-BookByName $DocName

        if (-not $Book)
        {
            $BookBody = [ordered]@{
                name        = $DocName
                description = "Import automatique"
                tags        = $TagsArray.ToArray()
            } | ConvertTo-Json -Depth 5

            $Book = Invoke-BookStackRequest `
                -Method Post `
                -Uri    "${BookStackUrl}/api/books" `
                -Body   $BookBody

            $Global:AllBooks += $Book
            Write-Host "    Livre créé : ID=$($Book.id)"
        }

        # Ajouter à l'étagère
        $ShelfDetails = Invoke-BookStackRequest -Method Get -Uri "${BookStackUrl}/api/shelves/$($Shelf.id)"
        $BookIds      = @($ShelfDetails.books | ForEach-Object { $_.id })
        if ($Book.id -notin $BookIds) { $BookIds += $Book.id }
        $ShelfBody    = [ordered]@{ name = $ShelfDetails.name; books = $BookIds } | ConvertTo-Json -Depth 5
        Invoke-BookStackRequest -Method Put -Uri "${BookStackUrl}/api/shelves/$($Shelf.id)" -Body $ShelfBody | Out-Null

        # Contenu dans la description du livre (texte brut, 2000 chars max)
$TempPage = Import-FileAsPage -BookId $Book.id -PageName $DocName -File $File

if ($null -ne $TempPage -and ($TempPage.id -is [int] -or $TempPage.id -is [long]))
{
    try
    {
        $PageDetails = Invoke-BookStackRequest `
            -Method Get `
            -Uri    "${BookStackUrl}/api/pages/$([int]$TempPage.id)"

        $PlainText = $PageDetails.markdown

        if ($PlainText)
        {
            if ($PlainText.Length -le 2000)
            {
                # Texte court → description du livre
                $ShortDesc = $PlainText.Trim()

                $DescBody = [ordered]@{
                    name             = $Book.name
                    description_html = "<p>$ShortDesc</p>"
                } | ConvertTo-Json -Depth 5

                Invoke-BookStackRequest `
                    -Method Put `
                    -Uri    "${BookStackUrl}/api/books/$($Book.id)" `
                    -Body   $DescBody | Out-Null

                Write-Host "    Description du livre mise à jour"
            }
            else
            {
                # Texte long → description tronquée + page dédiée
                $ShortDesc = $PlainText.Substring(0, 2000).Trim() + "..."

                $DescBody = [ordered]@{
                    name             = $Book.name
                    description_html = "<p>$ShortDesc</p>"
                } | ConvertTo-Json -Depth 5

                Invoke-BookStackRequest `
                    -Method Put `
                    -Uri    "${BookStackUrl}/api/books/$($Book.id)" `
                    -Body   $DescBody | Out-Null

                Write-Host "    Description du livre mise à jour (tronquée)"

                # Créer une page avec le texte complet
                $FullPage = New-BookStackPage `
                    -BookId   $Book.id `
                    -PageName "$DocName - Contenu complet" `
                    -Html     "<p>$([System.Web.HttpUtility]::HtmlEncode($PlainText))</p>"

                Write-Host "    Page contenu complet créée : ID=$($FullPage.id)"
            }
        }
    }
    catch
    {
        Write-Warning "    Erreur mise à jour description : $_"
    }
}
else
{
    Write-Warning "    Page retournée invalide, description non mise à jour (id=$($TempPage.id))"
}
    }

    # --------------------------------------------------
    # NIVEAU 2 : sous-dossiers enfants du dossier étagère
    # → Livre au nom du sous-dossier
    # --------------------------------------------------
    Get-ChildItem -Path $ShelfFolder.FullName -Directory | ForEach-Object {

        $SubDir     = $_
        $SubDirName = $SubDir.Name

        Write-Host ""
        Write-Host "  [Livre] : $SubDirName"

        $TagsArray = [System.Collections.Generic.List[object]]::new()
        $TagsArray.Add([pscustomobject]@{ name = $ShelfName;  value = "" })
        $TagsArray.Add([pscustomobject]@{ name = $SubDirName; value = "" })

        $Book = Get-BookByName $SubDirName

        if (-not $Book)
        {
            $BookBody = [ordered]@{
                name        = $SubDirName
                description = "Import automatique"
                tags        = $TagsArray.ToArray()
            } | ConvertTo-Json -Depth 5

            $Book = Invoke-BookStackRequest `
                -Method Post `
                -Uri    "${BookStackUrl}/api/books" `
                -Body   $BookBody

            $Global:AllBooks += $Book
            Write-Host "    Livre créé : ID=$($Book.id)"
        }
        else
        {
            Write-Host "    Livre existant : ID=$($Book.id)"
        }

        # Ajouter à l'étagère
        $ShelfDetails = Invoke-BookStackRequest -Method Get -Uri "${BookStackUrl}/api/shelves/$($Shelf.id)"
        $BookIds      = @($ShelfDetails.books | ForEach-Object { $_.id })
        if ($Book.id -notin $BookIds) { $BookIds += $Book.id }
        $ShelfBody    = [ordered]@{ name = $ShelfDetails.name; books = $BookIds } | ConvertTo-Json -Depth 5
        Invoke-BookStackRequest -Method Put -Uri "${BookStackUrl}/api/shelves/$($Shelf.id)" -Body $ShelfBody | Out-Null

        # Fichiers directement dans ce sous-dossier → Pages dans le livre
        Get-ChildItem -Path $SubDir.FullName |
        Where-Object { -not $_.PSIsContainer -and $_.Extension -match '\.(pdf|docx)$' } |
        ForEach-Object {

            $File    = $_
            $DocName = [System.IO.Path]::GetFileNameWithoutExtension($File.Name)

            Write-Host "    [Page] : $DocName"

            Import-FileAsPage `
                -BookId   $Book.id `
                -PageName $DocName `
                -File     $File | Out-Null
        }

        # Sous-sous-dossiers → Chapitres dans le livre
        Get-ChildItem -Path $SubDir.FullName -Directory | ForEach-Object {

            $SubSubDir     = $_
            $SubSubDirName = $SubSubDir.Name

            Write-Host "    [Chapitre] : $SubSubDirName"

            $Chapter = Get-ChapterByName -BookId $Book.id -ChapterName $SubSubDirName

            if (-not $Chapter)
            {
                $Chapter = New-BookStackChapter -BookId $Book.id -ChapterName $SubSubDirName
                Write-Host "      Chapitre créé : ID=$($Chapter.id)"
            }
            else
            {
                Write-Host "      Chapitre existant : ID=$($Chapter.id)"
            }

            # Fichiers dans le sous-sous-dossier → Pages dans le chapitre
            Get-ChildItem -Path $SubSubDir.FullName |
            Where-Object { -not $_.PSIsContainer -and $_.Extension -match '\.(pdf|docx)$' } |
            ForEach-Object {

                $File    = $_
                $DocName = [System.IO.Path]::GetFileNameWithoutExtension($File.Name)

                Write-Host "      [Page chapitre] : $DocName"

                Import-FileAsPage `
                    -BookId    $Book.id `
                    -ChapterId $Chapter.id `
                    -PageName  $DocName `
                    -File      $File | Out-Null
            }
        }
    }
}

Write-Host "Import terminé"

Stop-Transcript