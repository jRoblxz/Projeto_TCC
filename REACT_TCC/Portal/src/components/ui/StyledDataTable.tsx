import React from "react";

import DataTable, { TableProps } from "react-data-table-component";
import { useTheme } from "@/context/ThemeContext"; 
import { customLightStyles, customDarkStyles } from "@/context/dataTableContext";

interface StyledDataTableProps<T> extends TableProps<T> {
  // props extras se precisar
}

function StyledDataTable<T>({ ...props }: StyledDataTableProps<T>) {
  const { isDark } = useTheme();
  const themeStyles = isDark ? customDarkStyles : customLightStyles;

  return <DataTable {...props} customStyles={themeStyles} />;
}

export default StyledDataTable;
