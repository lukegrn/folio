import { beforeEach, expect, mock, test } from "bun:test";
import type { NextFunction, Request, Response } from "express";
import { errorHandler } from "../errorHandler";

const logError = mock();
const json = mock();
const status = mock((_status: number) => ({
  json,
}));

mock.module("../../logger", () => ({
  log: {
    error: logError,
  },
}));

const mockInputs = () => ({
  req: {
    path: "/",
    method: "GET",
  } as Request,
  res: {
    status,
    locals: {},
  } as unknown as Response,
  err: new Error("test message"),
  nextFn: () => ({}) as NextFunction,
});

beforeEach(() => {
  logError.mockClear();
  json.mockClear();
  status.mockClear();
});

test("logs an error", () => {
  const { err, req, res, nextFn } = mockInputs();
  errorHandler(err, req, res, nextFn);

  expect(logError.mock.calls.length).toBe(1);
  expect(logError.mock.calls?.[0]?.[0].message).toBe("test message");
});

test("responds with 500", () => {
  const { err, req, res, nextFn } = mockInputs();
  errorHandler(err, req, res, nextFn);

  expect(status.mock.calls.length).toBe(1);
  expect(status.mock.calls?.[0]?.[0]).toBe(500);
});

test("logs the request id when set", () => {
  const { err, req, res, nextFn } = mockInputs();

  res.locals.id = "id";
  errorHandler(err, req, res, nextFn);

  expect(logError.mock.calls.length).toBe(1);
  expect(logError.mock.calls?.[0]?.[0].id).toBe("id");
});

test("responds with prod json", () => {
  const holdEnv = process.env.NODE_ENV;
  process.env.NODE_ENV = "production";

  const { err, req, res, nextFn } = mockInputs();
  errorHandler(err, req, res, nextFn);

  expect(json.mock.calls.length).toBe(1);
  expect(json.mock.calls?.[0]?.[0].message).toBe("Internal Server Error");
  expect(json.mock.calls?.[0]?.[0].stack).toBeUndefined();

  process.env.NODE_ENV = holdEnv;
});

test("responds with dev json", () => {
  const holdEnv = process.env.NODE_ENV;
  process.env.NODE_ENV = "development";

  const { err, req, res, nextFn } = mockInputs();
  errorHandler(err, req, res, nextFn);

  expect(json.mock.calls.length).toBe(1);
  expect(json.mock.calls?.[0]?.[0].message).toBe("Internal Server Error");
  expect(json.mock.calls?.[0]?.[0].stack).toBeTypeOf("string");

  process.env.NODE_ENV = holdEnv;
});
