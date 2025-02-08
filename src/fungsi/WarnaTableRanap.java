/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package fungsi;

import java.awt.Color;
import java.awt.Component;
import javax.swing.JTable;
import javax.swing.table.DefaultTableCellRenderer;

/**
 *
 * @author Owner
 */
public class WarnaTableRanap extends DefaultTableCellRenderer {
    @Override
    public Component getTableCellRendererComponent(JTable table, Object value, boolean isSelected, boolean hasFocus, int row, int column){
        Component component = super.getTableCellRendererComponent(table, value, isSelected, hasFocus, row, column);
     
       // Get the value from column 22
        String cellValue = table.getValueAt(row, 22).toString();

        // Apply specific colors based on the value in column 22
        if (cellValue.equals("Belum Ada Diagnosis")) {
            if (row % 2 == 1){
            component.setBackground(new Color(255,244,244));
            component.setForeground(new Color(50,50,50));
        }else{
            component.setBackground(new Color(255,255,255));
            component.setForeground(new Color(50,50,50));
        } 
        } else if (cellValue.equals("10%")) {
            component.setBackground(new Color(255, 243, 109)); // Yellow for "10%"
            component.setForeground(new Color(120, 110, 50)); // Dark yellow text
        } else if (cellValue.equals("20%")) {
            component.setBackground(new Color(255, 200, 0)); // Orange for "20%"
            component.setForeground(Color.BLACK); // Black text
        } else if (cellValue.endsWith("%")) {
            try {
                double percentage = Double.parseDouble(cellValue.replace("%", ""));
                if (percentage < 10) {
                    component.setBackground(new Color(200, 0, 0)); // Red for less than 10%
                    component.setForeground(new Color(255, 230, 230)); // Light pink text
                } else if (percentage > 20) {
                    component.setBackground(new Color(0, 128, 0)); // Green for greater than 20%
                    component.setForeground(Color.WHITE); // White text
                }
            } catch (NumberFormatException e) {
                // Handle the case where the value is not a valid percentage
                component.setBackground(Color.WHITE);
                component.setForeground(Color.BLACK);
            }
        }
        return component;
    }

}
