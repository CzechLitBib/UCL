
APACHE
<pre>
        DocumentRoot /var/www/html

        &lt;Directory /var/www/html&gt;
                Options +Indexes
                HeaderName /include/HEADER.html
                ReadmeName /include/README.html
                AddIcon /icons/world2.gif ..
                IndexOptions FancyIndexing FoldersFirst NameWidth=* DescriptionWidth=* HTMLTable IgnoreClient
                IndexOptions SuppressHTMLPreamble SuppressDescription SuppressLastModified SuppressSize SuppressRules SuppressColumnSorting
                IndexIgnore .??* include *.csv
                IndexStyleSheet /include/STYLE.css
                AllowOverride Indexes Limit
        &lt;/Directory&gt;

        &lt;Directory /var/www/html/nkp/&gt;
                Options +Indexes
                HeaderName /nkp/include/HEADER.html
                ReadmeName /nkp/include/README.html
                AddIcon /icons/world2.gif ..
                IndexOptions FancyIndexing FoldersFirst NameWidth=* DescriptionWidth=* HTMLTable IgnoreClient
                IndexOptions SuppressHTMLPreamble SuppressDescription SuppressLastModified SuppressSize SuppressRules SuppressColumnSorting
                IndexIgnore .. .??* include
                IndexStyleSheet  /nkp/include/STYLE.css
                AllowOverride Indexes 
        &lt;/Directory&gt;

        &lt;Directory /var/www/html/nkp/*/&gt;
                Options +Indexes
                HeaderName /nkp/include/HEADER.html
                ReadmeName /nkp/include/README.html
                AddIcon /icons/world2.gif ..
                IndexOptions FancyIndexing FoldersFirst NameWidth=* DescriptionWidth=* HTMLTable IgnoreClient
                IndexOptions SuppressHTMLPreamble SuppressDescription SuppressLastModified SuppressSize SuppressRules SuppressColumnSorting
                IndexIgnore .??* include
                IndexStyleSheet  /nkp/include/STYLE.css
                AllowOverride Indexes 
        &lt;/Directory&gt;
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

