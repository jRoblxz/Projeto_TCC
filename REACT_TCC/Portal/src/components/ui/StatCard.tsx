import React from "react";

interface StatCardProps {
  label: string;
  value: number;
}

const StatCard: React.FC<StatCardProps> = ({ label, value }) => (
  <div
    className={`rounded-lg p-4 text-gray-900 dark:text-gray-200 shadow-md transform border-1 border-white hover:scale-105 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center`}
  >
    {/* <div className="mb-2">{icon}</div> */}
    <div className="mb-2">
      <span className="text-3xl font-bold text-center text-brand-secondary">
        {value}
      </span>
    </div>
    <div className="text-xs md:text-sm font-medium opacity-90 text-center">
      {label}
    </div>
  </div>
);

export default StatCard;
