import { TableStyles } from "react-data-table-component";

// 🔹 ESTILOS LIGHT MODE
export const customLightStyles: TableStyles = {
  headCells: {
    style: {
      backgroundColor: "##E6E6E6", // bg-gray-50
      color: "#374151", // text-gray-700
      fontSize: "13px",
      fontWeight: "600",
      textTransform: "uppercase",
      borderBottomColor: "#e5e7eb", // border-gray-200
    },
  },
  headRow: {
    style: {
      backgroundColor: "#E9EAEB",
      borderBottomColor: "#e5e7eb",
      minHeight: "52px",
    },
  },
  rows: {
    style: {
      backgroundColor: "#FAFAFA",
      color: "#1f2937",
      borderBottomColor: "#e5e7eb",
      minHeight: "48px",
      "&:hover": {
        backgroundColor: "#f3f4f6",
      },
    },

    stripedStyle: {
      backgroundColor: "#ffffff", 
      color: "#1f2937",
    },
  },
  pagination: {
    style: {
      backgroundColor: "#E9EAEB",
      color: "#6b7280",
      borderTopColor: "#e5e7eb",
    },
  },
  noData: {
    style: {
      backgroundColor: "#ffffff",
      color: "#6b7280",
      padding: "24px",
    },
  },
};

// 🔹 ESTILOS DARK MODE
export const customDarkStyles: TableStyles = {
  headCells: {
    style: {
      backgroundColor: "#111827", // bg-gray-50
      color: "#ffffff", // text-gray-700
      fontSize: "13px",
      fontWeight: "600",
      textTransform: "uppercase",
      borderBottomColor: "#e5e7eb", // border-gray-200
    },
  },
  headRow: {
    style: {
      backgroundColor: "#111827", // bg-gray-900
      //borderBottomColor: "#88e7eb",
      minHeight: "52px",
      text: "#FFFfff",
    },
  },
  rows: {
    style: {
      backgroundColor: "#1f2937",
      color: "#ffffff",
      borderBottomColor: "#e5e7eb",
      minHeight: "48px",
      "&:hover": {
        color: "#FFFFFF",
        backgroundColor: "#182338",
      },
    },

    stripedStyle: {
      backgroundColor: "#252f3f", 
      color: "#ffffff",
    },
  },

  

  // },
  // rows: {
  //   style: {
  //     backgroundColor: "#1f2937", // bg-gray-800
  //     color: "#d1d5db", // text-gray-300
  //     borderBottomColor: "#374151",
  //     minHeight: "48px",
  //     "&:hover": {
  //       backgroundColor: "#374151",
  //     },
  //   },
  // // },
  progress:{
    style: {
      backgroundColor: "transparent",
    }
  },
  pagination: {
    style: {
      backgroundColor: "#111827", // bg-gray-800
      color: "#9ca3af",
      borderTopColor: "#374151",
    },
  },
  noData: {
    style: {
      backgroundColor: "#1f2937",
      color: "#9ca3af",
      padding: "24px",
    },
  },
};
