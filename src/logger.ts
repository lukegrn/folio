import { Logger } from "tslog";
import { getConfig } from "./config/config";

export const log = new Logger({
  minLevel: getConfig().log.minLevel,
  type: "json",
});
