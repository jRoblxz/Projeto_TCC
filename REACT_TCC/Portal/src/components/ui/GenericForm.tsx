//**********************//
// GenericForm.tsx — Formulário Genérico Reutilizável
//**********************//

import React, { useState } from "react";
import { FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import Info from "@/components/ui/Info";
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from "@/components/ui/select";
import clsx from "clsx";
import { IMaskInput } from "react-imask";
import ModalGrid from "@/components/ui/ModalGrid";
import toast from "react-hot-toast";

export type GenericField = {
    name: string;
    label: string;
    type:
    | "text"
    | "textarea"
    | "number"
    | "decimal"
    | "moeda"
    | "select"
    | "date"
    | "readonly"
    | "modal";

    required?: boolean;

    options?: { value: string; label: string }[];

    modalConfig?: {
        title: string;
        apiEndpoint: string;
        columns: { key: string; label: string }[];
        onSelectLabelKey?: string; // ex: DESCRICAO, QUESTIONARIO
        extraParams?: (values: any) => Record<string, any>;
    };

    

    maxLength?: number;
    disabled?: boolean;
};

interface Props {
    title: string;
    fields: GenericField[];
    values: any;
    errors?: any;
    onChange: (field: string, value: any) => void;
    onSubmit: () => void;
    onCancel: () => void;
    loading?: boolean;
}

const currencyMask = {
    mask: "R$ num",
    blocks: {
        num: {
            mask: Number,
            scale: 2,
            signed: false,
            thousandsSeparator: ".",
            padFractionalZeros: true,
            normalizeZeros: true,
            radix: ",",
            mapToRadix: ["."],
        },
    },
};

const GenericForm: React.FC<Props> = ({
    title,
    fields,
    values,
    errors,
    onChange,
    onSubmit,
    onCancel,
}) => {
    const [animate, setAnimate] = useState(false);
    const [openModalField, setOpenModalField] = useState<string | null>(null);

    const fieldError = (field: string) => errors?.[field];

    return (
        <div>
            <h2 className="text-xl font-semibold dark:text-white text-gray-800 mb-4">
                {title}
            </h2>

            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    onSubmit();
                }}
                className="border-b-1 pb-4 flex flex-col items-center"
            >
                <div className="w-full max-w-2xl space-y-4">

                    {fields.map((f) => (
                        <div key={f.name} className="flex flex-col sm:flex-row sm:items-center gap-2">
                            {/* LABEL */}
                            <FieldLabel className="text-sm font-medium min-w-[110px]">
                                {f.label} {f.required ? "*" : ""}
                            </FieldLabel>

                            {/* INPUT */}
                            <div
                                className={clsx("flex items-center gap-2 w-full", {
                                    "animate-shake": fieldError(f.name),
                                })}
                            >
                                {f.type === "readonly" && (
                                    <Input
                                        disabled
                                        value={values[f.name] || ""}
                                        className="h-10 text-sm w-full bg-gray-100"
                                    />
                                )}

                                {f.type === "text" && (
                                    <Input
                                        value={values[f.name] || ""}
                                        onChange={(e) => {
                                            const value = f.maxLength
                                                ? e.target.value.slice(0, f.maxLength)
                                                : e.target.value;
                                            onChange(f.name, value);
                                        }}
                                        disabled={f.disabled}
                                        className={clsx(
                                            "h-10 text-sm w-full border rounded-md transition-all",
                                            fieldError(f.name)
                                                ? "border-red-500"
                                                : "border-gray-300"
                                        )}
                                    />
                                )}

                                {f.type === "number" && (
                                    <Input
                                        type="number"
                                        value={values[f.name] || ""}
                                        onChange={(e) => onChange(f.name, e.target.value)}
                                        className="h-10 text-sm w-full"
                                    />
                                )}

                                {f.type === "decimal" && (
                                    <Input
                                        type="number"
                                        step="0.01"
                                        value={values[f.name] || ""}
                                        onChange={(e) => onChange(f.name, e.target.value)}
                                        className="h-10 text-sm w-full"
                                    />
                                )}

                                {f.type === "moeda" && (
                                    <IMaskInput
                                        mask={currencyMask.mask}
                                        blocks={currencyMask.blocks}
                                        unmask={true}
                                        value={values[f.name] || ""}
                                        placeholder="R$ 0,00"
                                        onAccept={(val) => onChange(f.name, val)}
                                        className="h-10 text-sm w-full rounded-md border border-gray-300 px-3"
                                    />
                                )}

                                {f.type === "select" && (
                                    <Select
                                        value={values[f.name] || ""}
                                        onValueChange={(v) => onChange(f.name, v)}
                                    >
                                        <SelectTrigger className="h-10 text-sm w-full">
                                            <SelectValue placeholder="Selecione..." />
                                        </SelectTrigger>

                                        <SelectContent>
                                            {f.options?.map((opt) => (
                                                <SelectItem key={opt.value} value={opt.value}>
                                                    {opt.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                )}

                                {f.type === "modal" && (
                                    <Input
                                        readOnly
                                        value={values[f.name + "_text"] || ""}
                                        placeholder={`Selecionar ${f.label}`}
                                        className="h-10 text-sm w-full bg-gray-50 cursor-pointer"
                                        onClick={() => setOpenModalField(f.name)}
                                    />
                                )}

                                <Info label={f.label} />
                            </div>
                        </div>
                    ))}

                    {/* BUTTONS */}
                    <div className="flex justify-center gap-3 pt-6">
                        <button
                            type="button"
                            onClick={onCancel}
                            className="px-4 py-2 rounded-md bg-gray-500 text-white"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            className="px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white"
                        >
                            Confirmar
                        </button>
                    </div>
                </div>
            </form>

            {/* MODALS */}
            {fields.map((f) =>
                f.type === "modal" && openModalField === f.name ? (
                    <ModalGrid
                        key={f.name}
                        title={f.modalConfig!.title}
                        apiEndpoint={f.modalConfig!.apiEndpoint}
                        columns={f.modalConfig!.columns}
                        extraParams={f.modalConfig!.extraParams ? f.modalConfig!.extraParams(values) : undefined} // 👈 AQUI ENVIA O ID DA TRILHA
                        onClose={() => setOpenModalField(null)}
                        onSelect={(item) => {
                            const labelKey = f.modalConfig?.onSelectLabelKey || "DESCRICAO";
                            onChange(f.name, item.ID);
                            onChange(f.name + "_text", item[labelKey]);
                            setOpenModalField(null);
                        }}
                    />

                ) : null
            )}
        </div>
    );
};

export default GenericForm;
