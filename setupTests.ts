import { beforeAll } from "bun:test";

beforeAll(() => {
  process.env.DB_HOST = "host";
  process.env.DB_PORT = "1";
  process.env.DB_DATABASE = "database";
  process.env.DB_USER = "user";
  process.env.DB_PASS = "pass";
  process.env.LOG_MIN_LEVEL = "INFO";
});
