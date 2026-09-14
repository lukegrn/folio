import postgres from "postgres";
import { getConfig } from "../config/config";

export const sql = postgres({
  ...getConfig().database,
});
