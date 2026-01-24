import React, { ReactNode } from "react";
import { useTheme } from "@/context/ThemeContext";

interface CardProps {
  children: ReactNode;
  extra?: string;
  className?: string;
}

const Card: React.FC<CardProps> = ({ children, extra = "", className = "" }) => {
  const { isDark } = useTheme(); // ✅ dentro do componente

  return (
    <div
      className={`relative flex flex-col bg-clip-border shadow-lg rounded-xl
        ${isDark 
          ? "bg-gray-800 text-white" 
          : "bg-white text-gray-900"
        }
        ${extra} ${className}`}
    >
      {children}
    </div>
  );
};

export default Card;
