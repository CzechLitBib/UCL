package org.vufind.index;
/**
 * Title indexing routines.
 */

import org.marc4j.marc.Record;
import org.solrmarc.index.SolrIndexer;

/**
 * Title indexing class.
 */

public class CLB_TitleTools
{
   /**
    * Trim and strip traling slash from title.
    *
    * @param record current MARC record
    * @param filedSpec title subfields
    * @return stripped title
    */
    public String CLB_getTitle(final Record record, String fieldSpec) {

        String val = SolrIndexer.instance().getFirstFieldVal(record, fieldSpec);
       
	if (val != null) {
            String Title = val.replaceFirst("/$", "").trim();
            return Title;
        }
        return(null);
    }
}

