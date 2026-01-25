import React, { FC } from "react";
import { Menu, Bell, User, LogOut } from "lucide-react";
import { useNavigate } from "react-router-dom";
import toast from "react-hot-toast";

// Tipagem das props
interface HeaderProps {
  onMenuClick?: () => void; // função opcional que não recebe parâmetros
}

const Header: FC<HeaderProps> = ({ onMenuClick }) => {
  const navigate = useNavigate();

  const handleLogout = (): void => {
    localStorage.removeItem("token");
    toast.success("Logout realizado com sucesso!");
    navigate("/login");
  };

  return (
    <header className="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div className="flex items-center justify-between px-6 py-5">
        {/* Lado esquerdo */}
        <div className="flex items-center space-x-4">
          <button
            onClick={onMenuClick}
            className="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
          >
            <Menu className="w-5 h-5 text-gray-600 dark:text-gray-300" />
          </button>

          <div>
            <h1 className="text-xl font-semibold text-gray-900 dark:text-white">
              Grêmio Prudente
            </h1>
            <p className="text-sm text-gray-500 dark:text-gray-400">
              Bem-vindo
            </p>
          </div>
        </div>

        {/* Lado direito */}
        <div className="flex items-center space-x-4">
          {/* Notificações 
          <button className="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <Bell className="w-5 h-5 text-gray-600 dark:text-gray-300" />
            <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
          </button>*/}

          {/* Menu do usuário */}
          <div className="flex items-center space-x-3">
            {/* <div className="hidden sm:block text-right">
              <p className="text-sm font-medium text-gray-900 dark:text-white">
                Usuário Admin
              </p>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                admin@sistema.com
              </p>
            </div> */}

            <div className="flex items-center space-x-2">
              <button className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <User className="w-5 h-5 text-gray-600 dark:text-gray-300" />
              </button>

              <button
                onClick={handleLogout}
                className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors text-red-600 dark:text-red-400"
              >
                <LogOut className="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
