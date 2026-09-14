import { beforeEach, expect, test } from "bun:test";
import { getConfig, resetConfig } from "../config";

beforeEach(() => {
  resetConfig();
});

test("getConfig calls register in uninitialized", () => {
  expect(getConfig().database.port).toBe(1);
});

test("missing value throws error", () => {
  const holdHost = process.env.DB_HOST;
  process.env.DB_HOST = "";

  expect(getConfig).toThrowError("Malformed config");

  process.env.DB_HOST = holdHost;
});

test("unparseable int throws error", () => {
  const holdPort = process.env.DB_PORT;
  process.env.DB_PORT = "not a number";

  expect(getConfig).toThrowError("Malformed config");

  process.env.DB_PORT = holdPort;
});

test("enum key not found throws error", () => {
  const holdMinLevel = process.env.LOG_MIN_LEVEL;
  process.env.LOG_MIN_LEVEL = "not a key";

  expect(getConfig).toThrowError("Malformed config");

  process.env.LOG_MIN_LEVEL = holdMinLevel;
});
