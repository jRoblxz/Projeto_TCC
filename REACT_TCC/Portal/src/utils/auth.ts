export const getUserData = () => {
    const userData = localStorage.getItem("user_data");
    if (!userData) return null;
    try {
        return JSON.parse(userData);
    } catch (e) {
        return null;
    }
};

export const isUserAdmin = (): boolean => {
    const user = getUserData();
    // Verifica se o usuário existe e se a role é 'adm'
    return user?.role === 'adm'; 
};