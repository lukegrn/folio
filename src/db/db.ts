import postgres from "postgres";
import config from "../../appconf.json" with { type: "json" };

export const sql = postgres({
  ...config.db,
});
