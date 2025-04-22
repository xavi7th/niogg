export interface Award {
  id: number;
  recipient: string;
  organization: string;
  category: string;
}

export type SortOption = "default" | "recipient" | "category";
export type FilterCategory = string | null;
