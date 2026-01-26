import React from "react";
import { Moon, Sun } from "lucide-react";
import { useTheme } from "../../context/ThemeContext";



interface ThemeToggleProps {
  className?: string;
}

const ThemeToggle: React.FC<ThemeToggleProps> = ({ className = "" }) => {
  const { theme, toggleTheme } = useTheme();

  return (
    <button
      onClick={toggleTheme}
      className={`p-2 rounded-lg transition-colors hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 ${className}`}
      aria-label={theme === "light" ? "Ativar modo escuro" : "Ativar modo claro"}
      title={theme === "light" ? "Modo Escuro" : "Modo Claro"}
    >
      {theme === "light" ? (
        <Moon className="h-5 w-5 text-gray-700 dark:text-gray-300 transition-transform hover:scale-110" />
      ) : (
        <Sun className="h-5 w-5 text-yellow-500 transition-transform hover:scale-110 hover:rotate-90" />
      )}
    </button>
  );
};

export default ThemeToggle;
