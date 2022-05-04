package org.vufind.index;
/**
 * CLB - Custom date functions (based on UpdateDateTools).
 *
 * Copyright (C) Villanova University 2017.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

import java.time.format.DateTimeFormatter;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.ZoneOffset;
import java.util.Iterator;
import java.util.Set;
import java.util.List;
import org.solrmarc.index.SolrIndexer;
import org.marc4j.marc.Record;
import org.marc4j.marc.DataField;
import org.marc4j.marc.VariableField;
import org.marc4j.marc.Subfield;

public class CLB_DateTools
{
    private DateTimeFormatter marc005date = DateTimeFormatter.ofPattern("yyyyMMddHHmmss.S");
    private DateTimeFormatter marc008date = DateTimeFormatter.ofPattern("yyyyMMdd");
    private DateTimeFormatter marc046date = DateTimeFormatter.ofPattern("yyyyMMdd");
    private DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd'T'HH:mm:ss'Z'");//TODO: regular ISO_INSTANT

    private String normalize005Date(String input)
    {
        if (input == null) {
            input = "null";
        }

        LocalDateTime retVal;
        try {
            retVal = LocalDateTime.parse(input, marc005date);
        } catch(java.time.format.DateTimeParseException e) {
            retVal = LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC);
        }
        return retVal.format(formatter);
    }

    private String normalize008Date(String input)
    {
        if (input == null || input.length() < 6) {
            input = "null";
        }

	String year;
	if (input.matches("(0|1|2).*")) {// TODO: do it better..
	   year = String.format("20%s", input.substring(0, 6));
	} else {
	   year = String.format("19%s", input.substring(0, 6));
	}

        LocalDateTime retVal;
       	try {
            retVal = LocalDate.parse(year, marc008date).atStartOfDay();
        } catch(java.lang.StringIndexOutOfBoundsException | java.time.format.DateTimeParseException e) {
            retVal = LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC);
        }
        return retVal.format(formatter);
    }

    private String normalize046Date(String input)
    {

	String date;

	date = String.format("%8s", input).replace(' ','0');/* left pad with zero */

        LocalDateTime retVal;
       	try {
            retVal = LocalDate.parse(date, marc046date).atStartOfDay();
        } catch(java.lang.StringIndexOutOfBoundsException | java.time.format.DateTimeParseException e) {
            retVal = LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC);
        }
        return retVal.format(formatter);
    }

    /**
     * Extract the birth date from the MARC authority record.
     * @param record MARC record
     * @return Birth date.
     */
    public String CLB_getBirthDate(Record record) {
     
        List<VariableField> list046 = record.getVariableFields("046");
        if (list046 != null && !list046.isEmpty()) {
           DataField first;
           first = (DataField) list046.get(0);
           List<Subfield> subfields = first.getSubfields('f');
           if (subfields != null && !subfields.isEmpty()) {
              return normalize046Date(subfields.get(0).getData());
           }
        }

        return LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC).format(formatter);
    }

    /**
     * Extract the death date from the MARC authority record.
     * @param record MARC record
     * @return Death date.
     */
    public String CLB_getDeathDate(Record record) {
     
        List<VariableField> list046 = record.getVariableFields("046");
        if (list046 != null && !list046.isEmpty()) {
           DataField first;
           first = (DataField) list046.get(0);
           List<Subfield> subfields = first.getSubfields('g');
           if (subfields != null && !subfields.isEmpty()) {
              return normalize046Date(subfields.get(0).getData());
           }
        }

        return LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC).format(formatter);
    }

    /**
     * Extract the latest transaction date from the MARC record.
     * @param record MARC record
     * @return Latest transaction date.
     */
    public String CLB_getLatestTransactionDate(Record record) {
     
        Set<String> dates = SolrIndexer.instance().getFieldList(record, "005");
        if (dates != null) {
            Iterator<String> dateIter = dates.iterator();
            if (dateIter.hasNext()) {
                return normalize005Date(dateIter.next());
            }
        }

        return LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC).format(formatter);
    }

    /**
     * Extract the creation date from the MARC record.
     * @param record MARC record
     * @return Creation date.
     */
    public String CLB_getCreationDate(Record record) {
        Set<String> dates = SolrIndexer.instance().getFieldList(record, "008");
        if (dates != null) {
            Iterator<String> dateIter = dates.iterator();
            if (dateIter.hasNext()) {
                return normalize008Date(dateIter.next());
            }
        }

        return LocalDateTime.ofEpochSecond(0, 0, ZoneOffset.UTC).format(formatter);
    }

}

