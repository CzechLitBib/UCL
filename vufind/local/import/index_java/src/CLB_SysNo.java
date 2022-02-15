package org.vufind.index;
/**
 * CLB - Custom SysNo.
 *
 */

import java.util.Set;
import java.util.Iterator;
import org.solrmarc.index.SolrIndexer;
import org.marc4j.marc.Record;
import org.marc4j.marc.ControlField;

public class CLB_SysNo
{

    private String stripZero(String input)
    {
	String SysNo;
	SysNo = input.replaceAll("^0+", "");
      	return SysNo;
    }

    /**
     * Extract SysNo from MARC record and strip leading zeros.
     * @param record MARC record
     * @return SysNo.
     */
    public String CLB_getSysNo(Record record) {
        Set<String> sysno = SolrIndexer.instance().getFieldList(record, "001");
        if (sysno != null) {
            Iterator<String> sysnoIter = sysno.iterator();
            if (sysnoIter.hasNext()) {
               return stripZero(sysnoIter.next());
            }
        }

        return "999999999";// return invalid sysno
    }

}

