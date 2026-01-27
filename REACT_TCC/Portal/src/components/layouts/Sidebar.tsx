import React, { useState, useEffect } from "react";
import { Link, useLocation } from "react-router-dom";
import {
  LayoutDashboard,
  StretchHorizontal,
  Users,
  ChevronRight,
  UserCircle // Ícone para o perfil
} from "lucide-react";
import { motion as Motion, AnimatePresence } from "framer-motion";
import DefaultIcon from "../../assets/img/logo-copia.png"; // Renomeei para DefaultIcon
import ThemeToggle from "../ui/ThemeToggle";
import { getUserData, isUserAdmin } from "../../utils/auth"; // Importe o helper

// ... (Interfaces SubItem e MenuItem mantidas iguais) ...
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
  
  // Estados para dados do usuário
  const [user, setUser] = useState<any>(null);
  const [isAdmin, setIsAdmin] = useState(false);

  useEffect(() => {
    const data = getUserData();
    setUser(data);
    setIsAdmin(isUserAdmin());
  }, []);

  // --- DEFINIÇÃO DOS MENUS ---
  const menuItems: MenuItem[] = [];

  // 1. Dashboard (Apenas Admin)
  if (isAdmin) {
    menuItems.push({ title: "Dashboard", path: "/dashboard", icon: LayoutDashboard });
  }

  // 2. Itens Comuns
  menuItems.push(
    { title: "Peneiras", path: "/peneiras", icon: StretchHorizontal },
    { title: "Players", path: "/players", icon: Users }
  );

  // 3. Perfil (Apenas Jogador ou Todos, conforme sua preferência)
  // Assumindo que o ID do jogador está salvo em user.jogador_id ou user.id
  if (user) {
      // Só mostra o link se REALMENTE tivermos um jogador_id válido
      if (user.jogador_id) {
          menuItems.push({
              Group: "CONTA",
              title: "Meu Perfil",
              path: `/jogadores/${user.jogador_id}/edit`, // Usa direto o ID do jogador
              icon: UserCircle
          });
      }
  }

  const handleToggleSubmenu = (title: string) => {
    setOpenMenu((prev) => (prev === title ? null : title));
  };

  // Lógica da Imagem: Se tiver foto completa, usa ela. Senão, usa o logo padrão.
  // Ajuste 'pessoa.foto_url_completa' conforme a estrutura exata do seu JSON salvo
  const userPhoto = user?.pessoa?.foto_url_completa || user?.foto_url || DefaultIcon;
  const userName = user?.name || user?.pessoa?.nome_completo || "Usuário";

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
          fixed top-0 left-0 z-50 h-screen w-64 max-w-[16rem] bg-[#14244D] dark:bg-gray-900 
          shadow-xl transform transition-transform duration-300 ease-in-out
          ${isOpen ? "translate-x-0" : "-translate-x-full"}
          lg:translate-x-0 lg:sticky lg:top-0 lg:z-auto
        `}
      >
        <div className="flex flex-col h-full">
          
          {/* --- HEADER COM FOTO DO USUÁRIO --- */}
          <div className="flex-none flex items-center justify-between p-6 border-b border-gray-500/30 dark:border-gray-700">
            <div className="flex items-center space-x-3">
              <div className="w-12 h-12 rounded-full flex items-center justify-center overflow-hidden bg-white border-2 border-white/20">
                <img 
                    src={userPhoto} 
                    alt="User" 
                    className="w-full h-full object-cover"
                    onError={(e) => e.currentTarget.src = DefaultIcon} // Fallback se a URL quebrar
                />
              </div>
              <div className="flex flex-col">
                  <span className="text-sm font-bold text-gray-100 line-clamp-1" title={userName}>
                    {userName.split(' ')[0]} {/* Mostra só o primeiro nome pra caber */}
                  </span>
                  <span className="text-[10px] text-gray-400 uppercase">
                    {isAdmin ? 'Administrador' : 'Atleta'}
                  </span>
              </div>
            </div>
            <ThemeToggle />
          </div>

          {/* Navegação */}
          <nav className="flex-1 overflow-y-auto p-4 space-y-2">
            {menuItems.map((item) => {
              const IconComp = item.icon;
              const isActive = location.pathname === item.path;
              const isOpenSubmenu = openMenu === item.title;

              return (
                <div key={item.title}>
                  {item.Group && (
                    <h2 className="text-xs text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider mb-2 mt-6 first:mt-0 px-2">
                      {item.Group}
                    </h2>
                  )}

                  {/* Lógica de Renderização do Link (Igual ao original) */}
                  <Link
                      to={item.path || "#"}
                      onClick={onClose}
                      className={`w-full flex items-center justify-between p-3 rounded-lg transition-all duration-200 ${
                        isActive
                          ? "bg-[#8B0000] text-white shadow-md"
                          : "text-gray-300 hover:bg-white/10 hover:text-white"
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <IconComp className="w-5 h-5" />
                        <span className="font-medium">{item.title}</span>
                      </div>
                  </Link>
                </div>
              );
            })}
          </nav>

          {/* Rodapé */}
          <footer className="flex-none bg-[#0f1b3a] dark:bg-black/20 border-t border-gray-500/30 p-4">
            <p className="text-xs text-gray-500 text-center">
              © 2026 SparkLab
            </p>
          </footer>
        </div>
      </aside>
    </>
  );
};

export default Sidebar;