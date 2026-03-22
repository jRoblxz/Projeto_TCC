import React from "react";

interface InfoBoxProps {
  icon: React.ReactNode;
  title: string;
  value: string;
}

const InfoBox: React.FC<InfoBoxProps> = ({ icon, title, value }) => (
  <div className="flex items-center gap-4 dark:bg-gray-800 dark:border-gray-700 bg-[#f7fafc] p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
    <div className="w-12 h-12 rounded-xl bg-[#14244D] flex items-center justify-center shadow-lg shadow-indigo-200 dark:shadow-indigo-950 shrink-0">
      {icon}
    </div>
    <div>
      <h4 className="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-300 font-bold mb-1">
        {title}
      </h4>
      <p className="text-[#2d3748] dark:text-gray-100 font-bold text-lg leading-tight">
        {value}
      </p>
    </div>
  </div>
);

export default InfoBox;
