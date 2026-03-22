import React from "react";

interface StatCardProps {
  icon: React.ReactNode;
  label: string;
  value: number;
  color: string;
}

const StatCard: React.FC<StatCardProps> = ({ icon, label, value, color }) => (
  <div
    className={`${color} rounded-lg p-4 text-white shadow-md transform border-1 border-white hover:scale-105 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center`}
  >
    <div className="mb-2">{icon}</div>
    <div className="mb-2">
      <span className="text-3xl font-bold text-center">{value}</span>
    </div>
    <div className="text-xs md:text-sm font-medium opacity-90 text-center">
      {label}
    </div>
  </div>
);

export default StatCard;
