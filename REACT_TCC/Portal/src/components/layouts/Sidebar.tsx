import React, { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import {
  Table2,
  ChevronRight,
  Database,
  ChartBar,
  ChartColumn,
  ChartNoAxesColumn,
  ChartArea,
  LayoutDashboard,
  StretchHorizontal,
  Users,
} from "lucide-react";
import { motion as Motion, AnimatePresence } from "framer-motion";
import Icon from "../../assets/img/logo-copia.png"

import ThemeToggle from "../ui/ThemeToggle";

// Tipagens
interface SubItem {
  name: string;
  path: string;
  icon?: React.ComponentType<{ className?: string }>;
  pro?: boolean;
}

interface MenuItem {
  title: string;
  icon: React.ComponentType<{ className?: string }>;
  path?: string;
  Group?: string;
  subItens?: SubItem[];
}

interface SidebarProps {
  isOpen: boolean;
  onClose: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ isOpen, onClose }) => {
  const location = useLocation();
  const [openMenu, setOpenMenu] = useState<string | null>(null);

  const menuItems: MenuItem[] = [
    { title: "Dashboard", path: "/dashboard", icon: LayoutDashboard },
    { title: "Peneiras", path: "/peneiras", icon: StretchHorizontal },
    { title: "Players", path: "/players", icon: Users },
    {
      Group: "GESTÃO",
      title: "Indicador",
      icon: ChartNoAxesColumn,
      subItens: [
        { name: "Ind. Elementos", path: "/indicadores", icon: ChartArea },
      ],
    },
    {
      Group: "BQS",
      title: "Cadastro",
      icon: Database,
      subItens: [
        { name: "Trilha", path: "/trilha", icon: Table2 },
        { name: "Situacao", path: "/situacao", icon: Table2 },
        { name: "Tabela", path: "/tabela", icon: Table2 },
        { name: "Status", path: "/status", icon: Table2 },
        { name: "Charts", path: "/charts", icon: ChartBar },
      ],
    },
  ];

  const handleToggleSubmenu = (title: string) => {
    setOpenMenu((prev) => (prev === title ? null : title));
  };

  return (
    <>
      {/* Overlay para mobile */}
      {isOpen && (
        <div
          className="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
          onClick={onClose}
          aria-hidden="true"
        />
      )}

      {/* Sidebar */}
      <aside
        className={`
          fixed top-0 left-0 z-50 h-screen w-64 max-w-[16rem] bg-white dark:bg-gray-900 
          shadow-xl transform transition-transform duration-300 ease-in-out
          ${isOpen ? "translate-x-0" : "-translate-x-full"}
          lg:translate-x-0 lg:sticky lg:top-0 lg:z-auto
        `}
      >
        <div className="flex flex-col h-full">
          {/* Header */}
          <div className="flex-none flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div className="flex items-center space-x-3">
              <div className="w-10 h-10 rounded-full flex items-center justify-center">
                <img src={Icon} alt="Logo" className="w-10 h-10 flex items-center justify-center" />
              </div>
              <span className="text-xl font-bold text-gray-900 dark:text-white">
                João Pedro
              </span>
            </div>
            <ThemeToggle />
          </div>

          {/* Navegação */}
          <nav className="flex-1 overflow-y-auto p-4 space-y-2">
            {menuItems.map((item) => {
              const Icon = item.icon;
              const isActive = location.pathname === item.path;
              const isOpenSubmenu = openMenu === item.title;

              return (
                <div key={item.title}>
                  {item.Group && (
                    <h2 className="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider mb-2 mt-4 first:mt-0">
                      {item.Group}
                    </h2>
                  )}

                  {item.subItens ? (
                    <button
                      onClick={() => handleToggleSubmenu(item.title)}
                      className={`w-full flex items-center justify-between p-3 rounded-lg transition-all duration-200 ${
                        isOpenSubmenu
                          ? "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400"
                          : "text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <Icon className="w-5 h-5" />
                        <span className="font-medium">{item.title}</span>
                      </div>
                      <ChevronRight
                        className={`w-4 h-4 transition-transform ${
                          isOpenSubmenu ? "rotate-90" : ""
                        }`}
                      />
                    </button>
                  ) : (
                    <Link
                      to={item.path || "#"}
                      onClick={onClose}
                      className={`w-full flex items-center justify-between p-3 rounded-lg transition-all duration-200 ${
                        isActive
                          ? "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400"
                          : "text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <Icon className="w-5 h-5" />
                        <span className="font-medium">{item.title}</span>
                      </div>
                    </Link>
                  )}

                  {/* Submenu */}
                  <AnimatePresence>
                    {item.subItens && isOpenSubmenu && (
                      <Motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: "auto", opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.25 }}
                        className="ml-8 mt-1 space-y-1 overflow-hidden"
                      >
                        {item.subItens.map((sub) => {
                          const SubIcon = sub.icon || Icon;
                          const isSubActive = location.pathname === sub.path;

                          return (
                            <Link
                              key={sub.path}
                              to={sub.path}
                              onClick={onClose}
                              className={`w-full flex items-center justify-between p-2 rounded-lg transition-all duration-200 ${
                                isSubActive
                                  ? "bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-700"
                                  : "text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                              }`}
                            >
                              <div className="flex items-center space-x-3">
                                <SubIcon className="w-4 h-4" />
                                <span className="text-sm font-medium">
                                  {sub.name}
                                </span>
                              </div>
                            </Link>
                          );
                        })}
                      </Motion.div>
                    )}
                  </AnimatePresence>
                </div>
              );
            })}
          </nav>

          {/* Rodapé */}
          <footer className="flex-none bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 p-4">
            <p className="text-xs text-gray-500 dark:text-gray-400 text-center">
              © 2026 SparkLab
            </p>
          </footer>
        </div>
      </aside>
    </>
  );
};

export default Sidebar;