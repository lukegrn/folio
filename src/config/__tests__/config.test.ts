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

  expect(getConfig).toThrowError("Required value DB_HOST not found");

  process.env.DB_HOST = holdHost;
});

test("unparseable int throws error", () => {
  const holdPort = process.env.DB_PORT;
  process.env.DB_PORT = "not a number";

  expect(getConfig).toThrowError(
    `Unable to parse string ${process.env.DB_PORT} as int`,
  );

  process.env.DB_PORT = holdPort;
});

test("enum key not found throws error", () => {
  const holdMinLevel = process.env.LOG_MIN_LEVEL;
  process.env.LOG_MIN_LEVEL = "not a key";

  expect(getConfig).toThrowError(
    `String value ${process.env.LOG_MIN_LEVEL} is not a member of enum`,
  );

  process.env.LOG_MIN_LEVEL = holdMinLevel;
});
